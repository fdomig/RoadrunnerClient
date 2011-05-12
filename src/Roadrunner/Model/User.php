<?php
namespace Roadrunner\Model;

use Doctrine\ODM\CouchDB\View\Query;
use Doctrine\ODM\CouchDB\View\DoctrineAssociations;

/**
 * @Document
 */
class User extends BaseDocument {
	
	public final function __construct() {
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

	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
		$this->id = 'org.couchdb.user:' . $name;
	}
		
	public function getRoles() {
		return $this->roles;
	}
	
	public function setRoles($roles) {
		$this->roles = $roles;
	}
	
	public function setPassword($password)  {
		$salt = time();
		$password_sha = sha1($password+$salt);
		
		$this->salt = $salt;
		$this->password_sha = $password_sha;
	}
	
	static public function getAll($manager)  {
		return self::createQuery($manager,'users')
			->execute();
	}
	
	static public function createQuery($manager, $viewName) {
		return new Query(
			$manager->getConfiguration()->getHTTPClient(),
			'_users',
			'roadrunnerusers',
			$viewName,
			new DoctrineAssociations()
		);
	}
}