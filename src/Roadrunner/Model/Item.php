<?php
namespace Roadrunner\Model;

/**
 * @Document
 */
class Item {
	
	/** @Id */
	private $id;
	
	/** @Field(type="string") */
	private $name;
	
	/** @Field(type="string") */
	private $type = 'item';
	
	/** @Field(type="integer") */
	private $tempMin;

	/** @Field(type="integer") */	
	private $tempMax;
	
	public function getId() {
		return $this->id;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setTempMin($temp) {
		$this->tempMin = $temp;
	}

	public function getTempMin() {
		return $this->tempMin;
	}
		
	public function setTempMax($temp) {
		$this->tempMax = $temp;
	}
		
	public function getTempMax() {
		return $this->tempMax;
	}
}