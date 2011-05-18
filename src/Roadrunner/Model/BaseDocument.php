<?php
namespace Roadrunner\Model;

use Roadrunner\Provider\Service;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class BaseDocument {
	
 	public function __construct($type) {
        $this->type = $type;
    }
	
	/** @Id */
	protected $id;
	
	/** @Field(type="string") */
	protected $type;
	
	public function getId() {
		return $this->id;
	}
	
	static public function createQuery($manager, $viewName) {
		return new Query(
			$manager->getConfiguration()->getHTTPClient(),
			$manager->getConfiguration()->getDatabase(),
			'roadrunner',
			$viewName,
			new DoctrineAssociations()
		);
	}
	
	static protected function getManager()
	{
		return Service::getService('document_manager');
	}
	
	public function save($flush = true) {
		self::getManager()->persist($this);
		if ($flush) {
			self::getManager()->flush();
		}
	}
}