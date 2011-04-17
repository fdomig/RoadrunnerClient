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
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
		
}