<?php
namespace Roadrunner\Model;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class Log extends BaseDocument {
	
	public final function __construct() {
        parent::__construct('log');
    }
		
	/** @Field(type="string") */
	private $logType;
	
	/** @Field(type="string") */
	private $value;

	/** @Field(type="datetime") */	
	private $timestamp;
			
	public function getLogType() {
		return $this->logType;
	}
	
	public function getValue() {
		return $this->value;
	}
		
	public function getTimestamp() {
		return $this->timestamp;
	}
		
	static public function getForItemId($itemId)  {
		return parent::createQuery('logs')
			->setStartKey(array($itemId))
			->setEndKey(array($itemId, ""))
			->execute();
	}
}