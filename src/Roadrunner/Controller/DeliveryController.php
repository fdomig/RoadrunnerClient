<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Delivery;

class DeliveryController extends BaseController {
	
	public function executeIndex()
	{
		return $this->executeList();
	}
	
	public function executeList()
	{		
		return $this->render('delivery.list.twig', array(
			'delivery_list' => Delivery::getAll($this->getDocumentManager()),
		));
	}
	
}