<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Log;

class LogController extends BaseController {
	
	public function executeList() {	
		$itemId = $this->getRequest()->get('itemId');	
		return $this->render('log.list.twig', array(
			'log_list' => Log::getForItemId($itemId, $this->getDocumentManager()),
		));
	}
}