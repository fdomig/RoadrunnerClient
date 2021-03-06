<?php
namespace Roadrunner\Model;

use Roadrunner\Model\Address;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class Delivery extends BaseDocument {
	
	/** @Field(type="integer") */	 
	private $created_at;
	
	/** @Field(type="integer") */	
	private $modified_at;
	
    /** @EmbedOne */
    private $from_address;
    
    /** @EmbedOne */
    private $to_address;
    	
	/**
     * @ReferenceMany(targetDocument="Item", cascade={"persist"})
     */
	private $items;
	
	public final function __construct()
	{
        parent::__construct('delivery');
        $this->items       = new ArrayCollection();
    }
    
	/**
	 * @return Address
	 */
	public function getFromAddress()
	{
		return $this->from_address;
	}
	
	/**
	 * @param Address $address
	 */
	public function setFromAddress(Address $address)
	{
		if ($this->from_address !== $address) {
			$this->from_address = $address;
		}
	}
	
	/**
	 * @return Address
	 */
	public function getToAddress()
	{
		return $this->to_address;
	}
	
	/**
	 * @param Address $address
	 */
	public function setToAddress(Address $address)
	{
		if ($this->to_address !== $address) {
			$this->to_address = $address;
		}
	}
	
	/**
	 * @param Item $item
	 */
	public function addItem(Item $item)
	{
		$this->items[] = $item;
	}
    
	/**
	 * @return Ambigous <\Doctrine\Common\Collections\ArrayCollection, Item>
	 */
	public function getItems()
	{
		return $this->items;
	}
    
    /**
     * Removes an Item from this delivery
     * @param int $id
     */
    public function removeItem($id)
    {
    	foreach ($this->items as $i => $v) {
    		if ($v->getId() == $id) {
    			unset($this->items[$i]);
    			return;
    		}
    	}
    }
	
    /**
     * @return number
     */
    public function getCreatedAt()
    {
    	return $this->created_at;
    }
    
    /**
     * @return number
     */
    public function getModifiedAt()
    {
    	return $this->modified_at;
    }
    
    public function setModifiedAt()
    {
    	$this->modified_at = time();
    }

	public function getDirections()
	{	
		return array(
			"origin" => urlencode($this->getFromAddress()->__toString()),
			"destination" => urlencode($this->getToAddress()->__toString()),
		);
	}
	
	public function getRoutes()
	{
		$items = $this->getItems();
		$results = array();
		$routes = array();
		$rid = 1;
		$itemRoutes = array();
		
		foreach($items as $item) {
			$pos = $item->getPositionLogs();
			$route = array();
			$result = array();

			foreach($pos as $p) {
				$temp = $this->createRoutePos($p, $route, $result, $rid);
				$route = $temp['route'];
				$result = $temp['result'];
			}
			$temp = $this->addAndRefactorRoute($routes, $route, $results, $result, $rid);
			$routes = $temp['routes'];
			$results = $temp['results'];
			$rid = $temp['rid'];
			
			$itemRoutes[] = $this->newItemRoute($item, $rid);
		}
		return array(
			'results' => $results, 
			'items' => $itemRoutes,
		);
	}
	
	/**
	 * getRoute Utility Function
	 * @param array $p
	 * @param array $route
	 * @param array $result
	 * @param integer $rid
	 * @return array 
	 */
	private function createRoutePos($p, $route, $result, $rid)
	{
		// $p['value']['value'] => {lng, lat}
		$count = count($route);
		$position = $p['value'];
		$timestamp = $position['timestamp'];
		$add = false;
		// if new timestamp and same position take new log for that value 
		if ($this->isNewRecentLog($route, $position, $timestamp)) {
			$add = $count-1;
		// if no first timestamp has been set OR if new timestamp 
		} elseif ($this->isFirstOrNewLog($route, $position, $timestamp)) {
			$add = $count;
		}
		if (false !== $add) {
			$route[$add] = $p;
			$result[$add] = $this->createPosition(
				$p, $rid, Delivery::getMarkerImage($rid)
			);
		}
		return array(
			'route' => $route,
			'result' => $result,
		);
	}
	
	
	/**
	 * getRoute() Utility Function
	 * Checks if the log is the first or a newer one
	 * @return boolean
	 */
	private function isFirstOrNewLog($route, $position, $timestamp)
	{
		$count = count($route);
		return (
			empty($route) 
			|| $timestamp > $route[$count-1]['value']['timestamp']
			&& ($position['value'] != $route[$count-1]['value']['value']) 
			? true : false
		);
	}
	
	/**
	 * getRoute() Utility Function
	 * Checks if a Log is newer and has another Position than the last
	 * @param array $route
	 * @param double $position
	 * @param integer $timestamp
	 * @return boolean
	 */
	private function isNewRecentLog($route, $position, $timestamp)
	{
		$count = count($route);
		return (
			!empty($route) 
			&& $timestamp > $route[$count-1]['value']['timestamp'] 
			&& ($position['value'] == $route[$count-1]['value']['value']) 
			? true : false
		);
	}
	
	/**
	 * getRoute() Utility Function
	 * 
	 * Adds and Refactores the latest Route
	 * 
	 * @param array $routes
	 * @param array $route
	 * @param array $results
	 * @param array $result
	 * @param integer $rid
	 * @return array 
	 */
	private function addAndRefactorRoute($routes, $route, $results, $result, $rid)
	{
		if (empty($routes)) {
			$routes[] = $route;
			$results[] = $result;
		} else {
			foreach($routes as $r) {
				if ($r != $route) {
					$routes[] = $route;
					$results[] = $this->refactorRoute($result, ++$rid);
					break;
				}
			}
		}
		$results = $this->markPositionAsContainer($results, count($results)-1);
		
		return array(
			'results' => $results,
			'routes' => $routes,
			'rid'	=> $rid,
		);
	}
	
	/**
	 * getRoute Utility Function
	 * Marks a Position with a Container Image
	 * @param array $results
	 * @param integer $index
	 * @return array
	 */
	private function markPositionAsContainer($results, $index)
	{
		if (!empty($results)) {
			$results[$index] = $this->markContainer($results[$index]);
		}
		return $results;
	}
	
	/**
	 * getRoute() Utility Function
	 * 
	 * Returns a new valid ItemRoute array
	 * 
	 * @param Item $item
	 * @param integer $rid
	 * @return array 
	 */
	private function newItemRoute($item, $rid)
	{
		return array(
			'id' => $item->getId(), 
			'img' => Delivery::getMarkerImage($rid)
		);
	}
	
	/**
	 * Marks the route with the Container Image Path
	 * Image may be a truck
	 * @param array $route
	 * @return array
	 */
	protected function markContainer($route)
	{
		if (!empty($route)) {
			$container = count($route)-1;
			$route[$container]['img']['path'] = Delivery::getMarkerImage($route[$container]['rid'], true);
			$route[$container]['img']['width'] = 48;
			$route[$container]['img']['height'] = 32;
		}
		return $route;
	}
	
	/**
	 * Sets new Image Path and RID to the Route
	 * @param array $route
	 * @param integer $rid
	 * @return array
	 */
	protected function refactorRoute($route, $rid)
	{
		foreach($route as $k => $r) {
			$r['rid'] = $rid;
			$r['img']['path'] = Delivery::getMarkerImage($rid);
			$route[$k]= $r;
		}
		return $route;
	}
	
	/**
	 * Returns the Path to the marker Image
	 * @param integer $rid
	 * @return string
	 */
	static public function getMarkerImage($rid, $current = false) 
	{
		if ($current) {
			return '/img/marker_truck.png';
		}
		return '/img/marker_' . Delivery::mapRoute2Image($rid) . '.png';	
	}
	
	/**
	 * mapRoute2Image - Utility Function for createMarkerImage() 
	 * 
	 * @param route Integer
	 * @return String color
	 */
	static public function mapRoute2Image($rid)
	{	
		switch($rid) {
			case 1:
				return 'orange';
			case 2:
				return 'turkis';
			case 3:
				return 'yellow';
			case 4:
				return 'purple';
			default:
				return 'pink';		
		}
	}
	
	/**
	 * Creates a Position of a Route
	 * 
	 * @param Log POSSENSOR $logPos
	 * @param string $rid
	 * @param string $imgPath
	 * @param int $width
	 * @param int $height
	 * @return array('lat', 'lng', 'time', 'rid') 
	 */
	protected function createPosition($logPos, $rid, 
			$imgPath ="/img/marker_orange.png", $width = 20, $height = 32) 
	{
		$cpos = explode(',', $logPos['value']['value']);
		return array(
			'pos' => array(
				'lat' => trim($cpos[1]),
				'lng' => trim($cpos[0]),
			),
			'rid' => $rid,
			'img' => array(
				'path' => $imgPath,
				'width' => $width,
				'height' => $height,
			),
			'info' => array(
				'time'=> $logPos['value']['timestamp'],
				'msg' => 'Position reached at: '
			),
		);
	}
	
	public function save($flush = true)
	{
		$curTime = time();
		if ($this->created_at <= 0) {
			$this->created_at = $curTime;
		}   
		$this->modified_at = $curTime;
		parent::save($flush);
	}
	
	static public function getAll()
	{
		return self::createQuery('deliveries')->execute();
	}
	
	static public function find($id)
	{
		return self::getManager()->getRepository(__CLASS__)->find($id);
	}
}