<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Validator\DeliveryValidator;

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
		
		$errors = array();
		$validator = new DeliveryValidator();
		
		$delivery = Delivery::find($this->getRequest()->get('id'));
		
		$fromAddress = $this->getRequest()->get('from');
		$toAddress = $this->getRequest()->get('to');
		
		$errors = $validator->validateAddress($fromAddress, 'from-', '', $this->app);
		$errors = array_merge($errors, $validator->validateAddress($toAddress, 'to-', '', $this->app));
		
		$delivery->setFromAddress(new Address($fromAddress));
		$delivery->setToAddress(new Address($toAddress));			
		$delivery->setModifiedAt(time());
		
		$createItemList = explode(',',$this->app->escape($this->getRequest()->get('create-item-list')));
		$nrToRemove = (int)$this->getRequest()->get('nr-of-items-to-remove');
		$nrOfCreations = count($createItemList); 
		
		for ($i=0; $i < $nrOfCreations; $i++) {
			
			if (!empty($createItemList[$i])) {
				$properties = explode('|', $createItemList[$i]);
				$name = $properties[0];
				$mintemp = $properties[1];
				$maxtemp = $properties[2];
				
				$newItem = new Item();
				$newItem->setName($name);
				$newItem->setTempMin((int)$mintemp);
				$newItem->setTempMax((int)$maxtemp);
				
				$errors = array_merge($errors, $validator->validateItem(
						$name, $mintemp, $maxtemp, $i));
				
				$delivery->addItem($newItem);
			}
		}
		// remove sensors
		for ($i=0; $i < $nrToRemove; $i++) {
			$id = $this->app->escape($this->getRequest()->get('input-remove-item-' . $i));
			$delivery->removeItem($id);	
		}
		if (count($errors) == 0) { 
			$delivery->save();
			return $this->redirect('/delivery/view/' . $delivery->getId());
		}
		return $this->render('delivery.edit.twig', array(
			'delivery' => $delivery,
			'errors' => $errors,
			'form_action' => '/delivery/update/' . $delivery->getId(),
		));
	}
	
	/**
	 * Returns the Create Delivery Form
	 */
	public function executeCreate()
	{
		
		$errors = array();
		$validator = new DeliveryValidator();
		
		$fromAddress = $this->getRequest()->get('from');
		$toAddress = $this->getRequest()->get('to');
		
		$errors = $validator->validateAddress($fromAddress, 'from-', '', $this->app);
		$errors = array_merge($errors, $validator->validateAddress($toAddress, 'to-', '', $this->app));
		
		$delivery = new Delivery();
		
		$delivery->setFromAddress(new Address($fromAddress));
		$delivery->setToAddress(new Address($toAddress));
		
		$createItemList = explode(',',$this->app->escape($this->getRequest()->get('create-item-list')));
		$nrOfCreations = count($createItemList); 
		
		for ($i=0; $i < $nrOfCreations; $i++) {
			if (!empty($createItemList[$i])) {
				
				$properties = explode('|', $createItemList[$i]);
				$name = $properties[0];
				$mintemp = $properties[1];
				$maxtemp = $properties[2];
				
				$newItem = new Item();
				$newItem->setName($name);
				$newItem->setTempMin((int)$mintemp);
				$newItem->setTempMax((int)$maxtemp);
				
				$errors = array_merge($errors, $validator->validateItem(
						$name, $mintemp, $maxtemp, $i));
				
				$delivery->addItem($newItem);
			}
		}
		
		if (count($errors) == 0) { 
			$delivery->save();
			return $this->redirect('/delivery/view/' . $delivery->getId());
		}
		return $this->render('delivery.add.twig', array(
			'delivery' => $delivery,
			'errors' => $errors,
			'form_action' => '/delivery/create',
		));
	}
	
	/**
	 * Returns the Directions w.r.t. this Delivery
	 * @throws ControllerException
	 * @return string
	 */
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
	
	/**
	 * Returns the print formular
	 * @return string
	 */
	public function executePrint()
	{
		$delivery = Delivery::find($this->getRequest()->get('id'));
		
		if (is_null($delivery)) {
			throw new ControllerException("Delivery does not exist.");
		}
		$items = $delivery->getItems();
		$itemdata = array();
		foreach($items as $item) {
			$itemdata[] = $item->getPrintData();
		}
		
		return $this->render('delivery.print.twig', array(
			'delivery' => $delivery,
			'items' => $itemdata,
		));
	}
	
	/**
	 * Returns all Routes w.r.t. it's Delivery
	 * @throws ControllerException
	 * @return string
	 */
	public function executeRoutes()
	{
		
		if (!$this->getRequest()->isXmlHttpRequest()) {
			throw new ControllerException("Method not allowed.");
		}
		$delivery = Delivery::find($this->getRequest()->get('id'));
		return json_encode($delivery->getRoutes());
	}
}