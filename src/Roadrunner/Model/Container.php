<?php

namespace Roadrunner\Model;

class Container extends BaseDocument {
	
	/** @Field(type="string") */
	private $name;
	
	/** @Field(type="increment") */
	private $sensors;
	
	/**
	 * Constructor
	 */
	public function __construct() 
	{
		parent::__construct('container');
		$this->sensors = array();
	}
	

	/**
	 * Getter for $name
	 * @return string $name
	 */
	public function getName()
	{
		return $this->name;
	}
	
	/**
	 * Setter for $name
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}
	
	/**
	 * Adds a Sensor Url
	 * @param string $sensor
	 */
	public function addSensor($sensor) 
	{
		if (!array_search($sensor, $this->sensors)) {
			$this->sensors[] = $sensor;
		}
	}
	
	/**
	 * Getter for Sensors
	 * @return array <string>
	 */
	public function getSensors()
	{
		return $this->sensors;
	}
	
	static public function getAll()
	{
		return self::createQuery('container')->execute();
	}
	
	static public function find($id)
	{
		return self::getManager()->getRepository(__CLASS__)->find($id);
	}
}