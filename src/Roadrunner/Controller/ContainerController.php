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
	
	public function executeEdit()
	{
		return $this->render('container.edit.twig', array(
			'container' => Container::find($this->getRequest()->get('id')),
			'form_action' => '/container/update/' . $this->getRequest()->get('id'),
		));
	}
	
	public function executeAdd()
	{
		return $this->render('container.add.twig', array(
			'form_action' => '/container/create',
		)); 
	}
	
	public function executeUpdate()
	{
		$container = Container::find($this->getRequest()->get('id'));
		$container->setName($this->app->escape($this->getRequest()->get('name')));
		
		// add new sensors
		$nrOfSensors = (int) $this->getRequest()->get('nr-of-sensors');
		
		for ($i=0; $i < $nrOfSensors; $i++) {
			
			$uri = $this->app->escape($this->getRequest()->get('input-uri-hidden-' . $i));
			$container->addSensor($uri);
		}
		
		$container->save();
		
		return $this->redirect('/container/view/' . $container->getId());
	}
	
	public function executeCreate() 
	{	
		$container = new Container();
		$container->setName($this->app->escape($this->getRequest()->get('name')));
		
		$nrOfSensors = (int) $this->getRequest()->get('nr-of-sensors');
	
		for ($i=0; $i < $nrOfSensors; $i++) {
			
			$uri = $this->app->escape($this->getRequest()->get('input-uri-hidden-' . $i));
			$container->addSensor($uri);
		}
		
		$container->save();
		
		return $this->redirect('/container/view/' . $container->getId());
	}
	
}