<?php
namespace Roadrunner\Controller;

use Silex\Application;

abstract class BaseController {

	/** @var Silex\Application $application */
	private $app;

	/**
	 * @param Silex\Application $app
	 */
	public final function __construct(Application $app) {
		$this->app = $app;
	}
	
	/**
	 * @param string $name
	 * @return Symfony\Component\BrowserKit\Request $request
	 */
	protected function getRequest($name) {
		return $this->app['request']->get($name);
	}
	
	/**
	 *  @return Doctrine\ODM\CouchDB\DocumentManager $manager;
	 */
	protected function getDocumentManager() {
		return $this->app['document_manager'];
	}
	
	/**
	 * @return string - The HTML
	 * @param string $template
	 * @param array $values
	 */
	protected function render($template, array $values)  {
    	return $this->app['twig']->render($template, $values);
	}
	
	protected function redirect($to)
	{
		return $this->app->redirect($to);
	}
}