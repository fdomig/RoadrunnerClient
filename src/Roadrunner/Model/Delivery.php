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
	
	public final function __construct() {
        parent::__construct('delivery');
        $this->items = new ArrayCollection();
        $curTime = time();
        $this->created_at = $curTime;
		$this->modified_at = $curTime;
    }
    
	/**
	 * @return Address
	 */
	public function getFromAddress() {
		return $this->from_address;
	}
	
	/**
	 * @param Address $address
	 */
	public function setFromAddress(Address $address) {
		if ($this->from_address !== $address) {
			$this->from_address = $address;
		}
	}
	
	/**
	 * @return Address
	 */
	public function getToAddress() {
		return $this->to_address;
	}
	
	/**
	 * @param Address $address
	 */
	public function setToAddress(Address $address) {
		if ($this->to_address !== $address) {
			$this->to_address = $address;
		}
	}
	
	/**
	 * @param Item $item
	 */
 	public function addItem(Item $item) {
        $this->items[] = $item;
    }
    
    /**
     * @return Ambigous <\Doctrine\Common\Collections\ArrayCollection, Item>
     */
    public function getItems() {
    	return $this->items;
    }
    
    /**
     * Removes an Item from this delivery
     * @param int $id
     */
    public function removeItem($id) {
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
    public function getCreated_At() {
    	return $this->created_at;
    }
    
    /**
     * @return number
     */
    public function getModifiedAt() {
    	return $this->modified_at;
    }
    
    public function setModifiedAt() {
    	$this->modified_at = time();
    }

	public function getDirections()
	{	
		return array(
			"origin" => urlencode($this->getToAddress()->__toString()),
			"destination" => urlencode($this->getFromAddress()->__toString()),
		);
	}
	
	public function getRoutes()
	{
		$items = $this->getItems();
		$results = array();
		$routes = array();
		//var_dump($items);
		foreach($items as $item) {
			$pos = $item->getPositionLogs();
			$route = array();
			$result = array();
			$rid = $this->getRouteId();
			
			foreach($pos as $p) {
				
				// $p['value']['value'] => {lng, lat}
				$count = count($route);
				
				// if new timestamp and same position take new log for that value 
				if (!empty($route) && $p['value']['timestamp'] > $route[$count-1]['value']['timestamp'] 
					&& ($p['value']['value'] == $route[$count-1]['value']['value'])) {
					
					$route[$count-1] = $p;
					$result[$count-1] = $this->createPosition($p, $rid);
				
				// if no first timestamp has been set OR
				// if new timestamp 
				} elseif (empty($route) || $p['value']['timestamp'] > $route[$count-1]['value']['timestamp']
					&& ($p['value']['value'] != $route[$count-1]['value']['value'])) {
					$route[] = $p;
					$result[] = $this->createPosition($p, $rid);
				}	
			}
			// if first route add this route to all routes
			if (empty($routes)) {
				$routes[] = $route;
				$results[] = $result;
			} else {
				foreach($routes as $r) {
					if (count(array_diff($r, $route)) > 0) {
						$routes[] = $route;
						$results[] = $result;
						break;
					}
				}
			}
		}
		return $results;
	}
	
	/**
	 * Generates a Unique Route Id
	 * @return string
	 */
	protected function getRouteId()
	{
		return uniqid();
	}
	
	/**
	 * Creates a Position of a Route
	 * 
	 * @param Log POSSENSOR $logPos
	 * @param string $rid
	 * @return array('lat', 'lng', 'time', 'rid') 
	 */
	protected function createPosition($logPos, $rid) 
	{
		
		$cpos = explode(',', $logPos['value']['value']);
		return array(
			'lat' => trim($cpos[1]),
			'lng' => trim($cpos[0]),
			'time'=> $logPos['value']['timestamp'],
			'rid' => $rid,
		);
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