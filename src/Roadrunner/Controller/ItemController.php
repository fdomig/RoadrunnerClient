<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Item;

class ItemController extends BaseController {
	
	public function executeIndex()
	{
		return $this->executeList();
	}
	
	public function executeList()
	{		
		return $this->render('item.list.twig', array(
			'item_list' => Item::getAll($this->getDocumentManager()),
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
		$id = $this->getRequest()->get('id');
		$item = Item::find($this->getDocumentManager(), $id);

		return $this->render('item.view.twig', array(
			'item' => $item,
		));	
	}
	
	public function executeEdit()
	{
		$id = $this->getRequest()->get('id');
		$item = Item::find($this->getDocumentManager(), $id);
		
		return $this->render('item.edit.twig', array(
			'item' => $item,
			'form_action' => "/item/update/{$id}",
			'hello',
		));
	}
	
	public function executeUpdate()
	{
		
		$item = Item::find($this->getDocumentManager(), $this->getRequest()->get('id'));
		
		$name = $this->app->escape($this->getRequest()->get('name'));
		$tempMin = $this->app->escape($this->getRequest()->get('tempMin'));
		$tempMax = $this->app->escape($this->getRequest()->get('tempMax'));
		
		$item->setName($name);
		$item->setTempMin($tempMin);
		$item->setTempMax($tempMax);
		
		// FIXME: VALIDATE
		
		$manager = $this->getDocumentManager();
		$manager->persist($item);
		$manager->flush();
		
		return $this->redirect('/item/view/' . $item->getId());
	}
	
	public function executeCreate()
	{			
		$name = $this->app->escape($this->getRequest()->get('name'));
		$tempMin = $this->app->escape($this->getRequest()->get('tempMin'));
		$tempMax = $this->app->escape($this->getRequest()->get('tempMax'));
		
		if (empty($name)) {
			throw new \Exception("Name of item is not set.");
		}
		
		$manager = $this->getDocumentManager();
		$item = new Item();
		$item->setName($name);
		$item->setTempMin($tempMin);
		$item->setTempMax($tempMax);
		
		$manager->persist($item);
		$manager->flush();
		
		return $this->redirect('/item/view/' . $item->getId());
	}
	
}