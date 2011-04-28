<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Item;

class ItemController extends BaseController {
	
	public function executeIndex() {
		return link_to('item/add', 'Add new Item');
	}
	
	public function executeList() {
		$manager = $this->getDocumentManager();
		foreach ($manager->getRepository('Roadrunner\Model\Item') as $item) {
			var_dump($item);
		}
	}
	
	public function executeAdd() {
		return '<form action="'.url_for('/item/create').'" method="post">
			Name: <input type="text" value="" name="name" /><br />
			<input type="submit" value="Create Item" /></form>';
	}
	
	public function executeCreate()
	{	
		require_once __DIR__ . '/../../../lib/phpqrcode/qrlib.php';
		
		$name = $this->getRequest('name');
		
		if (empty($name)) {
			throw new \Exception("Name of item is not set.");
		}
		
		$manager = $this->getDocumentManager();
		$item = new Item();
		$item->setName($name);
		
		$manager->persist($item);
		$manager->flush();
		
		$file = md5($item->getId()) . '.png';
		if (!file_exists($file)) {
			\QRcode::png($item->getId(), __DIR__ . '/../../../web/cache/' . $file, 'L', 4, 2);
		}
		
		return 'Id of new Item "'.$item->getName().'" is: <pre>'
			. $item->getId() . '</pre><br />
			<img src="' . url_for('/cache/'.$file) . '" />';
	}
	
}