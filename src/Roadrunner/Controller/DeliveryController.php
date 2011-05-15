<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Item;

use Roadrunner\Model\Delivery;
use Roadrunner\Model\Address;

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
	
	public function executeView()
	{
		
		$delivery = Delivery::find(
			$this->getDocumentManager(), $this->getRequest()->get('id'));
			
		return $this->render('delivery.view.twig', array(
			'delivery' => $delivery,
		));
	}
	
	public function executeAdd()
	{
		return $this->render('delivery.add.twig', array(
			'form_action' => '/delivery/create',
		));
	}
	
	public function executeEdit()
	{
		return $this->render('delivery.edit.twig', array(
			'delivery' => Delivery::find(
					$this->getDocumentManager(), 
					$this->getRequest()->get('id')),
			'form_action' => '/delivery/update/' . $this->getRequest()->get('id'),
		));
	}
	
	public function executeUpdate()
	{
		$delivery = Delivery::find( $this->getDocumentManager(), 
					$this->getRequest()->get('id'));
		$delivery->setFrom_Address(new Address($this->getRequest()->get('from')));
		$delivery->setTo_Address(new Address($this->getRequest()->get('to')));			
		$delivery->setModified_At(time());
		
		$nrOfItems = (int) $this->getRequest()->get('nr-of-items');
		
		for ($i=0; $i < $nrOfItems; $i++) {
			
			$name = $this->app->escape($this->getRequest()->get('input-name-hidden-' . $i));
			$minTemp = $this->app->escape($this->getRequest()->get('input-min-temp-hidden-' . $i));
			$maxTemp = $this->app->escape($this->getRequest()->get('input-max-temp-hidden-' . $i));
			
			//FIXME: VALIDATE INPUT DATA
			
			$newItem = new Item();
			$newItem->setName($name);
			$newItem->setTempMin((int)$minTemp);
			$newItem->setTempMax((int)$maxTemp);
			
			$delivery->addItem($newItem);
		}
		
		$manager = $this->getDocumentManager();
		$manager->persist($delivery);
		$manager->flush();
		
		return $this->redirect('/delivery/view/' . $delivery->getId());
	}
	
	public function executeCreate()
	{
		
		$delivery = new Delivery();
		$delivery->setFrom_Address(new Address($this->getRequest()->get('from')));
		$delivery->setTo_Address(new Address($this->getRequest()->get('to')));
		
		$nrOfItems = (int) $this->getRequest()->get('nr-of-items');
		
		for ($i=0; $i < $nrOfItems; $i++) {
			
			$name = $this->app->escape($this->getRequest()->get('input-name-hidden-' . $i));
			$minTemp = $this->app->escape($this->getRequest()->get('input-min-temp-hidden-' . $i));
			$maxTemp = $this->app->escape($this->getRequest()->get('input-max-temp-hidden-' . $i));
			
			//FIXME: VALIDATE INPUT DATA
			
			$newItem = new Item();
			$newItem->setName($name);
			$newItem->setTempMin((int)$minTemp);
			$newItem->setTempMax((int)$maxTemp);
			
			$delivery->addItem($newItem);
		}
		
		$manager = $this->getDocumentManager();
		$manager->persist($delivery);
		$manager->flush();
		
		return $this->redirect('/delivery/view/' . $delivery->getId());
	}
	
}