<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Validator\UserValidator;

use Roadrunner\Model\User;

class UserController extends BaseController 
{	
	public function executeList()
	{
		return $this->render('user.list.twig', array(
			'user_list' => User::getAll(),
		));
	}
	
	public function executeAdd()
	{
		return $this->render('user.add.twig', 
			array(
				'form_action' => '/user/create',
			)
		);
	}
			
	public function executeCreate()
	{		
		$name = $this->getRequest()->get('name');
		$password = $this->getRequest()->get('password');
		$roles = $this->getRequest()->get('roles');
		
		$errors = array();
		$validator = new UserValidator();
		
		$errors = $validator->validateUser($name, $password, $roles);
		
		$user = new User();
		$user->setName($name);
		
		$user->setPassword($password);
		$user->setRoles($roles);
		
		if (count($errors) == 0) {
			$user->save();
			return $this->redirect('/user/list');
		}
		return $this->render('user.add.twig', array(
			'user' => $user,
			'errors' => $errors,
			'form_action' => '/user/create',
		));
	}
}