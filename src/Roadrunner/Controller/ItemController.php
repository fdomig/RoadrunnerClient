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
	
	public function executeTest() {
		return $this->render('item.twig', array("name"=>"Ein Test"));
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
		
		return 'Id of new Item "'.$item->getName().'" is: '
			. '<a href="' . url_for_db($item->getId()) . '">'
			. $item->getId() . '</a><br /><img src="'
			. url_for('/cache/'.$this->getItemImage($item->getId())) . '" />';
	}
	
	private function getItemImage($id)
	{
		$file = md5($id) . '.png';
		if (!file_exists($file)) {
			require_once __DIR__ . '/../../../lib/phpqrcode/qrlib.php';

			\QRcode::png($id, __DIR__ . '/../../../web/cache/'
				. $file, 'L', 4, 2);
		}
		
		return $file;
	}
	
}