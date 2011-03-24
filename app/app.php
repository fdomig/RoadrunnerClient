<?php
use Silex\Application;
use Roadrunner\Model\Item;
use Roadrunner\Model\Container;

$app = new Application();

$app->get('/', function() {
	return link_to('item/add', 'Add new Item');
});

$app->get('/item/add', function() {
	return '<form action="'.url_for('/item/create').'" method="post">
		<input type="text" value="" name="id" />
		<input type="submit" value="Create Item" /></form>';
});

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

$app->error(function($e) {
	return $e->getCode() . " - An error occured: " . $e->getMessage();
});

$app->after(function() {
	
});

$app->run();