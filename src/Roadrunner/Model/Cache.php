<?php
namespace Roadrunner\Model;

class Cache
{
	private $directory;
	
	private $linkpath;
	
	public function __construct() 
	{
		$this->directory = dirname(__FILE__) . '/../../../web/cache/';
		$this->linkpath  = '/cache/';
	}
	
	public function writeRaw($filename, $data)
	{
		file_put_contents($this->directory . $filename, $data);
	}
	
	public function getCacheDir()
	{
		return $this->directory;
	}
	
	public function exists($filename)
	{
		return file_exists($this->directory . $filename);
	}
	
	public function getPath($filename)
	{
		if ($this->exists($filename)) {
			return $this->linkpath . $filename;
		}
		return false;
	}
}