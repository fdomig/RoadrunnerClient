<?php
use Silex\Application;
use Roadrunner\Model\Item;
use Roadrunner\Model\Container;

$app = new Application();

$app->get('/', function() {
	return '<a href="/items/list">List all items</a>';
});

$app->get('/items/list', function() use ($dm) {
	$item = new Item();
	$item->setCode(4711);
	$dm->persist($item);
	$dm->flush();
});

$app->error(function($e) {
	return $e->getCode() . " - An error occured: " . $e->getMessage();
});

$app->after(function() {
	
});

$app->run();