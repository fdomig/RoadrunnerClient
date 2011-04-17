<?php
namespace Roadrunner\Controller;

use Silex\Application;

abstract class BaseController {

	/** @var Request $request */
	private $app;

	/**
	 * @param Application $app
	 */
	public final function __construct(Application $app) {
		$this->app = $app;
	}
	
	/**
	 * @return Symfony\Component\BrowserKit\Request $request
	 */
	protected function getRequest() {
		return $app->getReques();
	}
}