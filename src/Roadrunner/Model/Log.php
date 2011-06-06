<?php
namespace Roadrunner\Model;

use Doctrine\ODM\CouchDB\Attachment;
use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class Log extends BaseDocument {
	
	public final function __construct() {
        parent::__construct('log');
        $this->attachments = array();
    }
		
	/** @Field(type="string") */
	private $logType;
	
	/** @Field(type="string") */
	private $value;

	/** @Field(type="datetime") */	
	private $timestamp;
	
	/** @Attachments */
	public $attachments;
			
	public function getLogType() {
		return $this->logType;
	}
	
	public function getValue() {
		return $this->value;
	}
		
	public function getTimestamp() {
		return $this->timestamp;
	}
		
	public function getAttachments() {
		return $this->attachments;
	}
	
	public function addAttachment($attachment) {
		$this->attachments[] = $attachment;
	}
	
	
	
	static public function find($id)
	{
		return self::getManager()->getRepository(__CLASS__)->find($id);
	}
	
	static public function getForItemId($itemId)  {
		return parent::createQuery('logs', true)
			->setStartKey(array($itemId))
			->setEndKey(array($itemId, ""))
			->execute();
	}
}