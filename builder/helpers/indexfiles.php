<?php
/**
* JET - Joomla Extension Tools
*
* A Tool to build extensions out of a Joomla development environment
*
* @author Hannes Papenberg - hackwar - 02/2012
* @version 0.1
* @license GPL SA
* @link https://github.com/jtools/jet
*/

class JBuilderHelperIndexfiles extends JBuilderHelperBase {

	public $path = null;
	private $dir = null;
	private $counter = 0;
	private $name = null;

	public function __construct($options)
	{
		if (isset($options['path'])) {
			$this->path = $options['path'];
		}
		
		if (isset($options['name'])) {
			$this->name = $options['name'];
		}
	}
	public function setPath($path)
	{
		$this->path = $path;
	}

	public function execute()
	{
		if (is_null($this->path)) {
			throw new BuildException("Missing attribute 'path'");
		}
		if(!is_dir($this->path)) {
			throw new BuildException("'path' attribute not a valid path");
		}

		$this->readdir($this->path.'/');
		$this->log('['.$this->name.'] Added '.$this->counter.' index.html files to the project');
	}

	private function readdir($dir) {
		if(!file_exists($dir.'index.html')) {
			file_put_contents($dir.'index.html', '<html><body bgcolor="#FFFFFF"></body></html>');
			$this->counter++;
		}
		if($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(filetype($dir.$file) == 'dir' && !in_array($file, array('.','..'))) {
					$this->readdir($dir.$file.'/');
				}
			}
		} else {
			throw new Exception("Could not open path ".$dir);
		}
	}
}
