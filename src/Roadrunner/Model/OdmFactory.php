<?php

namespace Roadrunner\Model;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\CouchDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\CouchDB\DocumentManager;
use Doctrine\ODM\CouchDB\HTTP\SocketClient;
use Doctrine\ODM\CouchDB\Configuration;

class OdmFactory {

	static public function createOdm(
		$database, $host = 'localhost', $port = '5984', 
		$username = null, $password = null,
		array $path = array(),
		$namespace = 'Doctrine\ODM\CouchDB\Mapping\\')
	{
		$driver = self::loadDriverForDocuments($path, $namespace);
		
		$config = new Configuration();
		$config->setDatabase($database);
		$config->setMetadataDriverImpl($driver);

		$httpClient = new SocketClient($host, $port, $username, $password);
		$config->setHttpClient($httpClient);
		
		return DocumentManager::create($config);
	}
	
	static private function loadDriverForDocuments(array $path, $namespace)
	{
		if (empty($path)) $path = array('__DIR__');
		$driver = self::loadDriver($namespace);
		$driver->addPaths($path);
		
		return $driver;
	}
	
	static private function loadDriver($namespace)
	{
		$cache = new ArrayCache();
        $reader = new AnnotationReader($cache);
        $reader->setDefaultAnnotationNamespace($namespace);

        return new AnnotationDriver($reader);
	}
	
}
