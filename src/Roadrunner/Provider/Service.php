<?php

namespace Roadrunner\Provider;

use Roadrunner\Provider\ServiceException;

class Service {
	
	static private $provider = array();
	
	private function __construct() {}
	
	static public function registerProvider($provider)
	{
		self::$provider = $provider;
	}
	
	static public function getService($name)
	{
		return self::$provider[$name];
	}
}