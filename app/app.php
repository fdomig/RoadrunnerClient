<?php

use Symfony\Component\BrowserKit\Response;

use Roadrunner\Model\Item;
use Roadrunner\Model\Container;
use Roadrunner\Controller\ItemController;
use Roadrunner\Controller\LogController;
use Roadrunner\Controller\UserController;

/**
 * Root controller
 */
$app->get('/', array(new ItemController($app), 'executeIndex'));

/**
 * Add item controller
 */
$app->get('/item/add', array(new ItemController($app), 'executeAdd'));

/**
 * View item controller
 */
$app->get('/item/view/{id}', array(new ItemController($app), 'executeView'));

/**
 * List item controller
 */
$app->get('/item/list', array(new ItemController($app), 'executeList'));

/**
 * Create item controller
 */ 
$app->post('/item/create', array(new ItemController($app), 'executeCreate'));

/**
 * List log controller
 */
$app->get('/log/list/{itemId}', array(new LogController($app), 'executeList'));

/**
 * User log controller
 */
$app->get('/user/list', array(new UserController($app), 'executeList'));

/**
 * Add log controller
 */
$app->get('/user/add', array(new UserController($app), 'executeAdd'));

/**
 * Create log controller
 */
$app->get('/user/create', array(new UserController($app), 'executeCreate'));

/**
 * Error controller
 */
$app->error(function(Exception $e) use ($app) {
	if ($e instanceof NotFoundHttpException) {
		return new Response('What you are looking for does not exist', 404);
	}
	
	if (ENV != 'DEV') {
		$app['log']->addError(json_encode(array(
			'class'   => get_class($e),
			'message' => $e->getMessage(),
			'code'    => $e->getCode(),
			)));
	} else {
		echo '<pre>';
		print $e->getMessage() . "\n";
		debug_print_backtrace();
	}
	
	return new Response('Something bad happend.', 500);
});

/**
 * After controller
 */ 
$app->after(function() {	
});

$app->run();