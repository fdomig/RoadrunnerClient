<?php

namespace Roadrunner\Controller;

use Roadrunner\TestCase;
use Roadrunner\Controller\ItemController;

class ItemControllerTest extends TestCase {
	
	public function testExecuteIndexGivesCorrectOutput()
	{
		$client = $this->createClient();
		$crawler = $client->request('GET', '/');
		
		$this->assertTrue($client->getResponse()->isOk());
		$this->assertEquals(1, count($crawler->filter('h1:contains("The Roadrunner Project")')));
		$this->assertEquals(1, count($crawler->filter('h2:contains("List of Deliveries")')));
	}
	
}