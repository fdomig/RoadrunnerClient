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
		//$waypts = $this->getWayPoints();
		return array(
			"origin" => urlencode($this->getToAddress()->__toString()),
			"destination" => urlencode($this->getFromAddress()->__toString()),
			//"waypoints" => $waypts,
		);
	}
	
	public function getPosition()
	{
		// FIXME: implement better
		// get last position trough map reduce and for all delivery routes
		$items = $this->getItems();
		$pos = $items[0]->getPositionLogs();
		//var_dump($pos);
		$last = null;
		foreach($pos as $p) {
			if ($last == null || $p['value']['timestamp'] > $last['value']['timestamp']) {
				$last = $p;
			}	
		}
		$curpos = explode(',', $last['value']['value']);
		
		return array(
			'lat' => trim($curpos[1]),
			'lng' => trim($curpos[0]), 
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