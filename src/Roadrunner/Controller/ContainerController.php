<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Container;

class ContainerController extends BaseController
{
	public function executeList() 
	{	
		return $this->render('container.list.twig', array(
			'container_list' => Container::getAll(),
		));
	}
	
	public function executeView()
	{
		return $this->render('container.view.twig', array(
			'container' => Container::find($this->getRequest()->get('id')), 
		));
	}
	
	public function executeCreate() 
	{
		throw new \Exception("Not Yet Implemented");
	}
	
	public function executeAdd()
	{
		throw new \Exception("Not Yet Implemented");
	}
}