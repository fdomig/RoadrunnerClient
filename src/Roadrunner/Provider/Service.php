<?php

namespace Roadrunner\Provider;

use Roadrunner\Provider\ServiceException;

class Service {
	
	static private $provider = null;
	
	private function __construct() {}
	
	static public function registerProvider($provider)
	{
		self::$provider = $provider;
	}
	
	static public function getService($name)
	{
		if (is_null(self::$provider)) {
			throw new ServiceException("No service provider available.");
		}
		return self::$provider[$name];
	}
}