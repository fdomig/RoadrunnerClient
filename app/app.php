<?php

use Silex\Application;

use Symfony\Component\BrowserKit\Response;

use Roadrunner\Model\Item;
use Roadrunner\Model\Container;
use Roadrunner\Controller\ItemController;


$app = new Application();

$app['couchdb_conf'] = array(
	'host' => 'roadrunner.server',
	'port' => '5984',
);

/**
 * Root controller
 */
$app->get('/', array(new ItemController($app), 'executeIndex'));

/**
 * Add item controller
 */
$app->get('/item/add', function() {
	return '<form action="'.url_for('/item/create').'" method="post">
		<input type="text" value="" name="id" />
		<input type="submit" value="Create Item" /></form>';
});

/**
 * Create item controller
 */ 
$app->post('/item/create', function() use ($app) {
	require_once __DIR__ . '/../lib/phpqrcode/qrlib.php';
	$request = $app->getRequest();
	$id = $request->get('id');
	$file = md5($id) . '.png';
	if (!file_exists($file)) {
		QRcode::png($id, __DIR__ . '/../web/cache/' . $file, 'L', 4, 2);
	}
	return '<img src="' . url_for('/cache/'.$file) . '" />';
});

/**
 * Error controller
 */
$app->error(function(Exception $e) use ($log) {
	if ($e instanceof NotFoundHttpException) {
		return new Response('What you are looking for does not exist', 404);
	}
	
	$log->addError(json_encode(array(
		'class'   => get_class($e),
		'message' => $e->getMessage(),
		'code'    => $e->getCode(),
	)));
	
	return new Response('Something bad happend.', 500);
});

/**
 * After controller
 */ 
$app->after(function() {	
});

$app->run();