<?php
require_once __DIR__ . '/../vendor/silex.phar';

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Silex\Application;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Roadrunner\Model\OdmFactory;

use Silex\Extension\TwigExtension;

define('ENV', 'DEV'); // DEV | PROD

$app = new Application();

$app['config'] = array(
	'db.server' => 'roadrunner.server',
	'db.port' => '5984',
	'db.database' => 'roadrunner',
);

// class loader
$app['autoloader']->registerNamespaces(array(
	'Roadrunner'      => __DIR__ . '/../src',
	'Doctrine'    => array(
		__DIR__ . '/../vendor/couchdb-odm/lib',
		__DIR__ . '/../vendor/couchdb-odm/lib/vendor/doctrine-common/lib'
	),
	'Monolog'      => __DIR__ . '/../vendor/Monolog/src'
));

// couch db
$app['document_manager'] = $dm = OdmFactory::createOdm('roadrunner', 'roadrunner.server');

// twig
$app->register(new TwigExtension(), array(
    'twig.path'       => __DIR__.'/../views',
    'twig.class_path' => __DIR__.'/../vendor/Twig/lib',
));

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

function url_for_db($id) {
	return 'http://roadrunner.server:5984/roadrunner/' . $id;
}
