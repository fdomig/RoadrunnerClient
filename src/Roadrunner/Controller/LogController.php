<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Log;
use Roadrunner\Model\Cache;

class LogController extends BaseController {
	
	public function executeList()
	{		
		$itemId = $this->getRequest()->get('itemId');	
		$logs   = Log::getForItemId($itemId);
		$sigs   = array();
		
		foreach ($logs as $log){
		
			$l = Log::find($log['id']);
			
			if ($l->getLogType() == 'UNREGISTER') {		
				$ass = $l->getAttachments();
				if (array_key_exists('signature.png', $ass)) {
					
					/* 
					 * Store the file with prefix 'signature_' appending the
					 * item ID for this specific Log with extension 'png' if 
					 * it does not already exist in our Cache 
					 * $log['key']['0'] == item id for this log
					 */
					$filename = 'signature_'.$log['id'] . '.png';
					
					if (!$this->app['cache']->exists($filename)) {
						$this->app['cache']->writeRaw($filename, 
							$ass[$this->app['config']['img.state.delivered']]->getRawData());
						
					}
					
					$sigs[] = array(
						'id'        => $log['id'], 
						'signature' => $this->app['cache']->getPath($filename)
					);
				}
			}	
		}
		
		return $this->render('log.list.twig', array(
			'log_list'  => $logs,
			'signature' => $sigs,
		));
	}
}