<?php
namespace Roadrunner\Model;

use Roadrunner\Provider\Service;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class User extends BaseDocument {
	
	public final function __construct()
	{
        parent::__construct('user');
    }
		
	/** @Field(type="string") */
	private $name;
	
	/** @Field(type="array") */
	private $roles;
	
	/** @Field(type="string") */
	private $password_sha;
	
	/** @Field(type="string") */
	private $salt;

	public function getName()
	{
		return $this->name;
	}
	
	public function setName($name)
	{
		$this->name = $name;
		$this->id   = 'org.couchdb.user:' . $name;
	}
		
	public function getRoles()
	{
		return $this->roles;
	}
	
	public function setRoles($roles)
	{
		$this->roles = $roles;
	}
	
	public function setPassword($password)
	{
		$salt               = time();
		$this->salt         = $salt;
		$this->password_sha = sha1($password . $salt);
	}
	
	static public function getAll() 
	{
		return self::createQuery('users')->execute();
	}
	
	static public function createQuery($viewName)
	{
		return new Query(
			self::getManager()->getConfiguration()->getHTTPClient(),
			'_users',
			'roadrunnerusers',
			$viewName,
			new DoctrineAssociations()
		);
	}
	
	static protected function getManager()
	{
		return Service::getService('user_manager');
	}
	
	public function save() 
	{
		self::getManager()->getUnitOfWork()->registerManaged(
			$this, $this->getId(), null);
		self::getManager()->flush();
	}
}