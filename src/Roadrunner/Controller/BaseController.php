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
	 * @return Symfony\Component\BrowserKit\Request $request
	 */
	protected function getRequest() {
		return $this->app->getReques();
	}
	
	/**
	 *  @return Doctrine\ODM\CouchDB\DocumentManager $manager;
	 */
	protected function getDocumentManager() {
		return $this->app['document_manager'];
	}
}