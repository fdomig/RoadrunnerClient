<?php
require_once __DIR__ . '/../vendor/silex.phar';

require_once __DIR__ . '/conf.php';

use Silex\Application;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Roadrunner\Model\OdmFactory;
use Roadrunner\Provider\Service;

use Silex\Extension\TwigExtension;


$app = new Application();

$app['config'] = array(
	'db.server'        => '172.16.102.224',
	'db.port'          => '5984',
	'db.database'      => 'roadrunner',
	'db.user_database' => '_users',
);

// class loader
$app['autoloader']->registerNamespaces(array(
	'Roadrunner'  => array(
		__DIR__ . '/../src',
		__DIR__ . '/../tests',
	),
	'Doctrine'    => array(
		__DIR__ . '/../vendor/couchdb-odm/lib',
		__DIR__ . '/../vendor/couchdb-odm/lib/vendor/doctrine-common/lib',
	),
	'Monolog'     => __DIR__ . '/../vendor/Monolog/src'
));

// couch db
$app['document_manager'] = OdmFactory::createOdm(
	$app['config']['db.database'],
	$app['config']['db.server'],
	$app['config']['db.port'],
	'roadrunner', 'roadrunner'
);
$app['user_manager'] = OdmFactory::createOdm(
	$app['config']['db.user_database'],
	$app['config']['db.server'],
	$app['config']['db.port'],
	'roadrunner', 'roadrunner'
);

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

// register service provider
Service::registerProvider($app);

return $app;