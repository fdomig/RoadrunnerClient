<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Delivery;

use Roadrunner\Model\Validator\ItemValidator;

use Roadrunner\Model\Item;

class ItemController extends BaseController {
	
	public function executeIndex()
	{
		return $this->executeList();
	}
	
	public function executeList()  {
		return $this->render('item.list.twig', array(
			'item_list' => Item::getAll(),
		));
	}
	
	public function executeAdd()
	{
		return $this->render('item.add.twig', array(
			'form_action' => '/item/create',
		));
	}
	
	public function executeView()
	{
		$item = Item::find($this->getRequest()->get('id'));
		return $this->render('item.view.twig', array(
			'item' => $item,
		));	
	}
	
	public function executeEdit()
	{
		$id = $this->getRequest()->get('id');
		$item = Item::find($id);
		
		return $this->render('item.edit.twig', array(
			'item' => $item,
			'form_action' => "/item/update/{$id}",
		));
	}
	
	public function executeUpdate()
	{
		$errors = array();
		$validator = new ItemValidator();
		
		$item = Item::find($this->getRequest()->get('id'));
		
		$name = $this->app->escape($this->getRequest()->get('name'));
		$tempMin = $this->app->escape($this->getRequest()->get('tempMin'));
		$tempMax = $this->app->escape($this->getRequest()->get('tempMax'));
		
		$errors = $validator->validateSingleItem($name, $tempMin, $tempMax);
		
		$item->setName($name);
		$item->setTempMin($tempMin);
		$item->setTempMax($tempMax);
		
		if (count($errors) == 0) {
			$item->save();
			return $this->redirect('/item/view/' . $item->getId());
		}
		return $this->render('item.edit.twig', array(
			'item' => $item,
			'errors' => $errors,
			'form_action' => "/item/update/" . $item->getId(),
		)); 
	}
	
	/**
	 * @deprecated
	 */
	public function executeCreate()
	{			
		$errors = array();
		$validator = new ItemValidator();
		
		$name = $this->app->escape($this->getRequest()->get('name'));
		$tempMin = $this->app->escape($this->getRequest()->get('tempMin'));
		$tempMax = $this->app->escape($this->getRequest()->get('tempMax'));
		
		$errors = $validator->validateSingleItem($name, $tempMin, $tempMax);
		
		$item = new Item();
		$item->setName($name);
		$item->setTempMin($tempMin);
		$item->setTempMax($tempMax);
		
		if (count($errors) == 0) {
			$item->save();
			return $this->redirect('/item/view/' . $item->getId());
		}
		return $this->render('item.add.twig', array(
			'item' => $item,
			'errors' => $errors,
			'form_action' => '/item/create',
		));
	}
	
	public function executeStatus()
	{
		$item = $this->findAjaxItem();
		
		return json_encode(array(
			'id'     => $item->getId(),
			'status' => $item->getStatus()
		));
	}
	
	public function executeRoute()
	{
		$item = $this->findAjaxItem();
		
		return json_encode(array(
			'id' => $item->getId(),
			'route' => $item->getRoute()
		));
	}
	
	public function executeTempLogs()
	{
		$item = $this->findAjaxItem();
		
		return json_encode(array(
			'id' => $item->getId(),
			'name' => $item->getName(),
			'logs' => $item->getTempLogs()
		));
	}
	
	private function findAjaxItem()
	{
		if (!$this->getRequest()->isXmlHttpRequest()) {
			throw new ControllerException("Method not allowed.");
		}

		$id = $this->getRequest()->get('id');
		$item = Item::find($id);
		
		if (is_null($item)) {
			throw new ControllerException("Item does not exist.");
		}
		
		return $item;
	}
	
	/**
	 * Returns the Current ItemStatus
	 * @throws ControllerException
	 */
	public function executeItemStatusImage()
	{
		if (!$this->getRequest()->isXmlHttpRequest()) {
			throw new ControllerException("Method not allowed.");
		}

		$id = $this->getRequest()->get('id');
		$item = Item::find($id);
		
		if (is_null($item)) {
			throw new ControllerException("Item does not exist.");
		}
		return json_encode(array(
			'id' => $item->getId(), 
			'img' => $item->getStatusMarkerImage()
		));
		
	}
	
}