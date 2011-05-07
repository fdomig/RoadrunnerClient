<?php
namespace Roadrunner\Model;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class Item extends BaseDocument {
	
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
		$file = md5($this->id) . '.png';
		if (!file_exists($file)) {
			require_once __DIR__ . '/../../../lib/phpqrcode/qrlib.php';

			\QRcode::png($this->id, __DIR__ . '/../../../web/cache/'
				. $file, 'L', 4, 2);
		}
		return url_for('cache/' . $file);
	}
	
	static public function getAll($manager)
	{
		return parent::createQuery($manager, 'items')->execute();
	}
}