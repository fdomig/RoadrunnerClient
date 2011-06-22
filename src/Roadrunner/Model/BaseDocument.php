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
	
	public function getId()
	{
		return $this->id;
	}
	
	/**
	 * Creates a CouchDB Query
	 * @param string $view
	 * @param bool $includeDocs
	 * @return \Doctrine\ODM\CouchDB\View\Query
	 */
	static public function createQuery($view, $includeDocs = false)
	{
		$query = new Query(
			self::getManager()->getConfiguration()->getHTTPClient(),
			self::getManager()->getConfiguration()->getDatabase(),
			'roadrunner',
			$view,
			new DoctrineAssociations()
		);
		$query->setIncludeDocs($includeDocs);
		return $query;
	}
	
	static protected function getManager()
	{
		return Service::getService('document_manager');
	}
	
	public function save($flush = true)
	{
		self::getManager()->persist($this);
		if ($flush) {
			self::getManager()->flush();
		}
	}
}