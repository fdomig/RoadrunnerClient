<?php

namespace Roadrunner;

use Silex\WebTestCase;

class TestCase extends WebTestCase {
	
	public function createApplication()
	{
	    return require __DIR__ . '/../../app/app.php';
	}
}