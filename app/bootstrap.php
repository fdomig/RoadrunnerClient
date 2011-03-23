<?php
require_once __DIR__ . '/../vendor/silex.phar';

use Symfony\Component\ClassLoader\UniversalClassLoader;
use Roadrunner\Database\CouchDB;

// class loader
$loader = new UniversalClassLoader();
$loader->registerNamespaces(array(
	'Roadrunner'      => __DIR__ . '/../src',
	'Doctrine'    => array(
		__DIR__ . '/../vendor/couchdb-odm/lib',
		__DIR__ . '/../vendor/couchdb-odm/lib/vendor/doctrine-common/lib'
	),
));
$loader->register();

// couch db
$config = new \Doctrine\ODM\CouchDB\Configuration();
$httpClient = new \Doctrine\ODM\CouchDB\HTTP\SocketClient();
$config->setHttpClient($httpClient);

$dm = \Doctrine\ODM\CouchDB\DocumentManager::create($config);

