<?php
namespace Roadrunner\Model;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class Log {
	
	/** @Id */
	private $id;
	
	/** @Field(type="string") */
	private $type = 'log';
	
	/** @Field(type="string") */
	private $logType;
	
	/** @Field(type="string") */
	private $value;

	/** @Field(type="datetime") */	
	private $timestamp;
	
	public function getId() {
		return $this->id;
	}
		
	public function getLogType() {
		return $this->logType;
	}
	
	public function getValue() {
		return $this->value;
	}
		
	public function getTimestamp() {
		return $this->timestamp;
	}
		
	static public function getAll($manager)
	{
		return self::createQuery($manager)->execute();
	}
	
	static private function createQuery($manager) {
		return new Query(
			$manager->getConfiguration()->getHTTPClient(),
			$manager->getConfiguration()->getDatabase(),
			'roadrunner',
			'logs',
			new DoctrineAssociations()
		);
	}
}