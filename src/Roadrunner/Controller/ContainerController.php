<?php
namespace Roadrunner\Controller;

use Roadrunner\Model\Validator\ContainerValidator;

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
		$errors = array();
		$validator = new ContainerValidator();
		
		$container = Container::find($this->getRequest()->get('id'));
		$name = $this->app->escape($this->getRequest()->get('name'));
		$container->setName($name);
		
		$errors = $validator->validateContainer($name);
		
		// add new sensors
		$nrToRemove = (int) $this->getRequest()->get('nr-of-sensors-to-remove');
		$sensorCreateList = explode(',', $this->app->escape($this->getRequest()->get('create-sensor-list')));
		
		// add sensors
		for ($i=0; $i < count($sensorCreateList); $i++) {
			(!empty($sensorCreateList[$i])) ? $container->addSensor($sensorCreateList[$i]) : null;
		}
		
		// remove sensors
		for ($i=0; $i < $nrToRemove; $i++) {
			$uri = $this->app->escape($this->getRequest()->get('input-remove-sensor-' . $i));
			$container->removeSensor($uri);	
		}
		
		if (count($errors) == 0) {
			$container->save();
			return $this->redirect('/container/view/' . $container->getId());
		}
		return $this->render('container.edit.twig', array(
			'container' => $container,
			'errors' => $errors,
			'form_action' => '/container/update/' . $container->getId(),
		));
	}
	
	public function executeCreate() 
	{	
		$errors = array();
		$validator = new ContainerValidator();
		
		$container = new Container();
		$name = $this->app->escape($this->getRequest()->get('name'));
		$container->setName($name);
		
		$errors = $validator->validateContainer($name);
		
		$sensorCreateList = explode(',', $this->app->escape($this->getRequest()->get('create-sensor-list')));
		
		// add sensors
		for ($i=0; $i < count($sensorCreateList); $i++) {
			(!empty($sensorCreateList[$i])) ? $container->addSensor($sensorCreateList[$i]) : null;
		}
		
		if (count($errors) == 0) {
			$container->save();
			return $this->redirect('/container/view/' . $container->getId());		
		}
		return $this->render('container.add.twig', array(
			'container' => $container,
			'errors' => $errors,
			'form_action' => '/container/create',
		));
	}
	
}