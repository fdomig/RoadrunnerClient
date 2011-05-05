<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Item;

class ItemController extends BaseController {
	
	public function executeIndex() {
		return $this->executeList();
	}
	
	public function executeList() {		
		return $this->render('item.list.twig', array(
			'item_list' => Item::getAll($this->getDocumentManager()),
		));
	}
	
	public function executeAdd() {
		return $this->render('item.add.twig', array(
			'form_action' => url_for('/item/create'),
		));
	}
	
	public function executeCreate()
	{			
		$name = $this->getRequest('name');
		$tempMin = $this->getRequest('tempMin');
		$tempMax = $this->getRequest('tempMax');
		
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
		
		return $this->render('item.create.twig', array(
			'item' => $item
		));
	}
	
}