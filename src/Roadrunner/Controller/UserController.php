<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\User;

class UserController extends BaseController {
	
	public function executeList() {	
		return $this->render('user.list.twig', array(
			'user_list' => User::getAll($this->getDocumentManager()),
		));
	}
	
	public function executeAdd()  {
		return $this->render('user.add.twig', array(
			'form_action' => '/user/create',
		));
	}
	public function executeCreate()  {			
		$name = $this->getRequest()->get('name');
		$password = $this->getRequest()->get('password');
		
		//TODO: change
		$roles = array('default'); 
		
		if (empty($name)) {
			throw new \Exception("Name of the user is not set.");
		}
		if (empty($password)) {
			throw new \Exception("Password of the user is not set.");
		}
		
		$manager = $this->getUsersManager();;
		$user = new User();
		$user->setName($name);
		
		$user->setPassword($password);
		$user->setRoles($roles);

		$manager->getUnitOfWork()->registerManaged($user, $user->getId(), null);
		$manager->flush();
		
		return $this->redirect('/user/list');
	}
}