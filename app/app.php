<?php
use Silex\Application;
use Roadrunner\Model\Item;
use Roadrunner\Model\Container;

$app = new Application();

$app->get('/', function() {
	return '<a href="/items/list">List all items</a>';
});

$app->get('/item/list', function() use ($dm) {
	$item = new Item();
	$item->setCode(4711);
	$dm->persist($item);
	$dm->flush();
});

$app->get('/item/create/{id}', function($id) {
	require_once __DIR__ . '/../lib/phpqrcode/qrlib.php';
	$file = md5($id) . '.png';
	if (!file_exists($file)) {
		QRcode::png($id, __DIR__ . '/../web/cache/' . $file, 'L', 4, 2);
	}
	return '<img src="/cache/'.$file.'" />';
});

$app->error(function($e) {
	return $e->getCode() . " - An error occured: " . $e->getMessage();
});

$app->after(function() {
	
});

$app->run();