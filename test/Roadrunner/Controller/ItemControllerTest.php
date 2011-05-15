<?php

namespace Roadrunner\Controller;

use Roadrunner\TestCase;
use Roadrunner\Controller\ItemController;

class ItemControllerTest extends TestCase {

	public function setUp()
	{
		$this->controller = new ItemController($this->createApplication());
	}
	
	public function tearDown()
	{
		$this->controller = null;
	}
	
	public function testExecuteIndex()
	{
		
	}
	
}