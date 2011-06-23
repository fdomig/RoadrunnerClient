<?php
namespace Roadrunner\Model;

use Roadrunner\Provider\Service;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class Item extends BaseDocument {
	
	static private $maxTempLogs = 20;
	
	const STATE_HIGH = "HIGH";
	const STATE_LOW = "LOW";
	const STATE_NORMAL = "NORMAL";
	
	public final function __construct()
	{
        parent::__construct('item');
    }
	
	/** @Field(type="string") */
	private $name;
		
	/** @Field(type="integer") */
	private $tempMin;

	/** @Field(type="integer") */	
	private $tempMax;
		
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setTempMin($temp)
	{
		$this->tempMin = $temp;
	}

	public function getTempMin()
	{
		return $this->tempMin;
	}
		
	public function setTempMax($temp)
	{
		$this->tempMax = $temp;
	}
		
	public function getTempMax()
	{
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
	
	public function getPrintData()
	{
		return array(
			'mintemp' => $this->tempMin,
			'maxtemp' => $this->tempMax,
			'qrcode' => $this->getImage(),
			'name' => $this->getName(),
		);
	}

	public function getDelivery()
	{
		$result = self::createQuery('deliveryforitem')
			->setKey($this->getId())
			->execute();
		return Delivery::find($result[0]['id']);
	}
	
	public function getStatus()
	{
		$result = self::createQuery('itemstatus')
			->setKey($this->getId())
			->execute()->toArray();
		return $result[0]['value']['status'];
	}
	
	public function getStatusLogType()
	{
		$result = self::createQuery('itemstatus')
			->setKey($this->getId())
			->execute()->toArray();
		return (array_key_exists('logType', $result[0]['value'])) 
			? $result[0]['value']['logType'] : ItemStatus::REGISTER;
	}
	
	public function getRoute()
	{
		$result = self::createQuery('itemroute')
			->setStartKey(array($this->getId()))
			->setEndKey(array($this->getId(), '', ''))
			->setGroupLevel(3);
		return $result->execute();
	}
	
	public function getStatusMarkerImage()
	{
		$type = $this->getStatusLogType();
		switch($type) {
			case ItemStatus::REGISTER:
				return '/img/marker_registered.png';
			case ItemStatus::UNREGISTER:
				return '/img/marker_delivered.png';
			default:
				return '/img/marker_truck.png';
		}
	}
	
	public function getSignature()
	{
		foreach(Log::getForItemId($this->getId()) as $log) {
			
			if (ItemStatus::UNREGISTER == $log['value']['logType']) {
				$ass = Log::find($log['id'])->getAttachments();
				if (array_key_exists('signature.png', $ass)) {
					
					/* 
					 * Store the file with prefix 'signature_' appending the
					 * item ID for this specific Log with extension 'png' if 
					 * it does not already exist in our Cache 
					 * $log['key']['0'] == item id for this log
					 */
					$filename = 'signature_'.$log['id'] . '.png';
					if (!Service::getService('cache')->exists($filename)) 
					{
						$config = Service::getService('config');
						Service::getService('cache')->writeRaw($filename, 
							$ass[$config['img.state.delivered']]->getRawData());
					}
					return Service::getService('cache')->getPath($filename);
				}
			}
		}
		return false;
	}
	
	/**
	 * Gets all Position Log Entries for this Item
	 * If $unvalid is false the result set will only contain valid position
	 * Log entries. If $unvalid is true it will not only contain valid entries
	 * but also POSERROR entries.
	 *   
	 * @param bool $valid 
	 */
	public function getPositionLogs($unvalid = false)
	{
		$logs = array();
		foreach (Log::getForItemId($this->getId()) as $log) {
			if ('POSSENSOR' == $log['value']['logType']) {
				$logs[] = $log;
			}
			if ('POSERROR' == $log['value']['logType'] && $unvalid) {
				$logs[] = $log;
			}
		}
		return $logs;
	}
	
	public function getTempLogs()
	{
		$logs = $this->getTempLogData();
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
			if ($i-- < 1 || $this->getTempState($v) != self::STATE_NORMAL) {		
				$data[] = array(
					'timestamp' => (int) $log['value']['timestamp'],
					'value' => round($v, 2),
					'state' => $this->getTempState($v), 
				);
				$i = $max;
			}
		}
		
		return $data;
	}
	
	/**
	 * Get Temperature State
	 * @param float $temp
	 * @return string
	 */
	private function getTempState($temp) 
	{
		if ($this->getTempMax() < $temp) {
			return self::STATE_HIGH;
		} else if ($this->getTempMin() > $temp) {
			return self::STATE_LOW;
		}
		return self::STATE_NORMAL;
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