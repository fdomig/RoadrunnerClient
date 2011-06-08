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
			'delivery_list' => Delivery::getAll(),
		));
	}
	
	public function executeView()
	{
		
		$delivery = Delivery::find($this->getRequest()->get('id'));
			
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
			'delivery' => Delivery::find($this->getRequest()->get('id')),
			'form_action' => '/delivery/update/' . $this->getRequest()->get('id'),
		));
	}
	
	public function executeUpdate()
	{
		$delivery = Delivery::find($this->getRequest()->get('id'));
		
		$delivery->setFromAddress(new Address($this->getRequest()->get('from')));
		$delivery->setToAddress(new Address($this->getRequest()->get('to')));			
		$delivery->setModifiedAt(time());
		
		$createItemList = explode(',',$this->app->escape($this->getRequest()->get('create-item-list')));
		$nrToRemove = (int) $this->getRequest()->get('nr-of-items-to-remove');
	
		for ($i=0; $i < count($createItemList); $i++) {
			
			//FIXME: VALIDATE INPUT DATA
			if (!empty($createItemList[$i])) {
				$properties = explode('|',$createItemList[$i]);
				$newItem = new Item();
				$newItem->setName($properties[0]);
				$newItem->setTempMin((int)$properties[1]);
				$newItem->setTempMax((int)$properties[2]);
				
				$delivery->addItem($newItem);
			}
		}
		// remove sensors
		for ($i=0; $i < $nrToRemove; $i++) {
			$id = $this->app->escape($this->getRequest()->get('input-remove-item-' . $i));
			$delivery->removeItem($id);	
		}
		
		$delivery->save();
		
		return $this->redirect('/delivery/view/' . $delivery->getId());
	}
	
	public function executeCreate()
	{
		
		$delivery = new Delivery();
		$delivery->setFromAddress(new Address($this->getRequest()->get('from')));
		$delivery->setToAddress(new Address($this->getRequest()->get('to')));
		
		$createItemList = explode(',',$this->app->escape($this->getRequest()->get('create-item-list')));
		
		for ($i=0; $i < count($createItemList); $i++) {
			if (!empty($createItemList[$i])) {
				//FIXME: VALIDATE INPUT DATA
				$properties = explode('|',$createItemList[$i]);
				$newItem = new Item();
				$newItem->setName($properties[0]);
				$newItem->setTempMin((int)$properties[1]);
				$newItem->setTempMax((int)$properties[2]);
				
				$delivery->addItem($newItem);
			}
		}
		
		$delivery->save();
		
		return $this->redirect('/delivery/view/' . $delivery->getId());
	}
	
	public function executeDirections()
	{
		if (!$this->getRequest()->isXmlHttpRequest()) {
			throw new ControllerException("Method not allowed.");
		}

		$id = $this->getRequest()->get('id');
		$delivery = Delivery::find($id);
		
		if (is_null($delivery)) {
			throw new ControllerException("Delivery does not exist.");
		}
		
		return json_encode($delivery->getDirections());
	}
	
	
	public function executeWayPoints()
	{
		if (!$this->getRequest()->isXmlHttpRequest()) {
			throw new ControllerException("Method not allowed.");
		}
		$delivery = Delivery::find($this->getRequest()->get('id'));
		// TODO:	return valid positions 
		// 			interpolate all results and limit the output to max 20 or
		// 			check speed to calculate maximum result limit
		
		return json_encode($delivery->getPosition());
	}
}