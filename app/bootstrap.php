<?php
require_once __DIR__ . '/../vendor/silex.phar';

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Silex\Application;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ODM\CouchDB\DocumentManager;
use Doctrine\ODM\CouchDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ODM\CouchDB\HTTP\SocketClient;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$app = new Application();

// class loader
$app['autoloader']->registerNamespaces(array(
	'Roadrunner'      => __DIR__ . '/../src',
	'Doctrine'    => array(
		__DIR__ . '/../vendor/couchdb-odm/lib',
		__DIR__ . '/../vendor/couchdb-odm/lib/vendor/doctrine-common/lib'
	),
	'Monolog'      => __DIR__ . '/../vendor/Monolog/src',
));

// couch db
$config = new Doctrine\ODM\CouchDB\Configuration();
$config->setMetadataDriverImpl(new AnnotationDriver(new AnnotationReader()));
$httpClient = new SocketClient('roadrunner.server', '5984');
$config->setHttpClient($httpClient);
$app['document_manager'] = $dm = DocumentManager::create($config);

// logger
$app['log'] = new Logger('roadrunner');
$app['log']->pushHandler(new StreamHandler(
	'file://' . __DIR__ . '/../log/error.log',
	Logger::ERROR
));

// helper functions
function link_to($url, $name) {
	return sprintf('<a href="%s">%s</a>', url_for($url), $name);
}

function url_for($url) {
	return 'http://' . $_SERVER['HTTP_HOST'] . '/' . trim($url, '/');
}
