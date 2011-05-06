<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Log;

class LogController extends BaseController {
	
	public function executeList() {		
		return $this->render('log.list.twig', array(
			'log_list' => Log::getAll($this->getDocumentManager()),
		));
	}
}