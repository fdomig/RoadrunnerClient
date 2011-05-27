<?php
namespace Roadrunner\Model;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class Item extends BaseDocument {
	
	static private $maxTempLogs = 20;
	
	public final function __construct() {
        parent::__construct('item');
    }
	
	/** @Field(type="string") */
	private $name;
		
	/** @Field(type="integer") */
	private $tempMin;

	/** @Field(type="integer") */	
	private $tempMax;
		
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setTempMin($temp) {
		$this->tempMin = $temp;
	}

	public function getTempMin() {
		return $this->tempMin;
	}
		
	public function setTempMax($temp) {
		$this->tempMax = $temp;
	}
		
	public function getTempMax() {
		return $this->tempMax;
	}
	
	public function getImage()
	{
		$file = md5($this->getId()) . '.png';
		if (!file_exists($file)) {
			require_once __DIR__ . '/../../../lib/phpqrcode/qrlib.php';
			
			\QRcode::png($this->getId(), __DIR__ . '/../../../web/cache/'
				. $file, 'L', 4, 2);
		}
		return '/cache/' . $file;
	}

	public function getStatus()  {
		$result = self::createQuery('itemstatus')
			->setKey($this->getId())
			->execute()->toArray();
			
		return $result[0]['value']['status'];
	}
	
	public function getRoute()
	{
		$result = self::createQuery('itemroute')
			->setStartKey(array($this->getId()))
			->setEndKey(array($this->getId(), '', ''))
			->setGroupLevel(3);
			
		return $result->execute();
	}
	
	public function getTempLogs()
	{
		$logs = $this->getTempLogData();
		$minTemp = $this->getTempMin();
		$maxTemp = $this->getTempMax();
		$data = array();
		
		// max entries are calculated by settings
		// however, if there are less logs than we allow as maximum,
		// we use all of them
		$max = $i = (count($logs) >= self::$maxTempLogs)
			? count($logs) / self::$maxTempLogs - 1
			: 0;
		
		foreach ($logs as $log) {
			$v = $log['value']['value'];
			
			// do we have to add this log entry by count()-rule or
			// do we have to add it because of a critical temp
			if ($i-- < 1 || $v < $minTemp || $v > $maxTemp) {		
				$data[] = array(
					'timestamp' => (int) $log['value']['timestamp'],
					'value' => round($v, 2),
				);
				$i = $max;
			}
		}
		
		return $data;
	}
	
	private function getTempLogData()
	{
		$logs = array();
		foreach (Log::getForItemId($this->getId()) as $log) {
			if ('TEMPSENSOR' == $log['value']['logType']) {
				$logs[] = $log;
			}
		}
		return $logs;
	}
	
	static public function getAll()
	{
		return self::createQuery('items')->execute();
	}
	
	static public function find($id)
	{
		return self::getManager()->getRepository(__CLASS__)->find($id);
	}
}