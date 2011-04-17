<?php
namespace Roadrunner\Controller;

class ItemController extends BaseController {
	
	public function executeIndex() {
		return link_to('item/add', 'Add new Item');
	}
	
	public function executeList() {
		$manager = $this->getDocumentManager();
		$manager->getRepository('Roadrunner\Model\Item');
	}
	
	public function executeAdd() {
		return '<form action="'.url_for('/item/create').'" method="post">
			<input type="text" value="" name="id" />
			<input type="submit" value="Create Item" /></form>';
	}
	
	public function executeCreate() {
		
	}
	
}