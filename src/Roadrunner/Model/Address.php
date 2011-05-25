<?php

namespace Roadrunner\Model;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;


/**
 * @EmbeddedDocument
 */
class Address {

    /** @ No Id for embedded */
    protected $id;
    
    /** @String */
    protected $name;
    
    /** @String */
    protected $country;
    
    /** @String */
    protected $zip;
    
    /** @String */
    protected $city;
    
    /** @String */
    protected $street;

	public function __construct($data = array())
	{
		if (!empty($data)) {
			$this->setName($data['name']);
			$this->setStreet($data['street']);
			$this->setZip($data['zip']);
			$this->setCity($data['city']);
			$this->setCountry($data['country']);
		}
	}
    
    public function setName($name) {
    	$this->name = $name;
    }
    
    public function getName() {
    	return $this->name;
    }
    
    public function setCountry($country) {
        $this->country = $country;
    }

    public function setZip($zip) {
        $this->zip = $zip;
    }

    public function setCity($city) {
        $this->city = $city;
    }
    
	public function setStreet($street) {
    	$this->street = $street;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getCountry() {
        return $this->country;
    }

    public function getZip() {
        return $this->zip;
    }

    public function getCity() {
        return $this->city;
    }
    
    public function getStreet() {
    	return $this->street;
    }

	public function __toString()
	{
		return $this->getStreet() . ', ' . $this->getZip()
			. ' ' . $this->getCity();
	}
}