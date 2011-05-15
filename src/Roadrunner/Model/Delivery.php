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
	public function getFrom_Address() {
		return $this->from_address;
	}
	
	/**
	 * @param Address $address
	 */
	public function setFrom_Address(Address $address) {
		if ($this->from_address !== $address) {
			$this->from_address = $address;
		}
	}
	
	/**
	 * @return Address
	 */
	public function getTo_Address() {
		return $this->to_address;
	}
	
	/**
	 * @param Address $address
	 */
	public function setTo_Address(Address $address) {
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
     * @return number
     */
    public function getCreated_At() {
    	return $this->created_at;
    }
    
    /**
     * @return number
     */
    public function getModified_At() {
    	return $this->modified_at;
    }
    
    public function setModified_At() {
    	$this->modified_at = time();
    }
	
	static public function getAll($manager)
	{
		return self::createQuery($manager, 'deliveries')->execute();
	}
	
	static public function find($manager, $id)
	{
		return $manager->getRepository(__CLASS__)->find($id);
	}
}