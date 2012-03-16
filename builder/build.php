<?php
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__));

require_once '../libraries/import.php';

jimport('joomla.application.cli');

class HelloYou extends JCli
{
	protected $extensions = array();
	
	protected $verbose = false;
	
	public function execute()
	{
		$this->out(str_repeat('=', 79));
		$text = 'JOOMLA! EXTENSION TOOLS';
		$this->out(str_repeat(' ', (79 - strlen($text))/2).$text);
		$this->out(str_repeat('=', 79));

		if(count($this->input->args)) {
			$this->loadPropertiesFromFile($this->input->args[0]);
		} else {
			$this->loadPropertiesFromInput();
		}
		
		$this->loadPropertiesFromCLI();
		
		foreach($this->extensions as $extension) {
			$this->buildExtension($extension);
		}
		
		$this->close(0);
	}

	protected function loadPropertiesFromFile($file)
	{
		if(is_file($file)) {
			$path = $file;
		} elseif(is_file(JPATH_BASE.'/'.$file)) {
			$path = JPATH_BASE.'/'.$file;
		} else {
			$this->out('FATAL ERROR: Could not read config file from given location');
			$this->close(1);
		}
		$this->out('[config] Reading config data from '.$path);
	}

	protected function loadPropertiesFromInput()
	{
		
	}

	protected function loadPropertiesFromCLI()
	{
		if($this->input->get('v') || $this->input->get('verbose')) {
			$this->verbose = true;
			$this->out('[config] verbose mode enabled');
		}
	}

	protected function buildExtension($extension)
	{
		/**
		 * We are going to load different classes for building extensions in here,
		 * one for each extension type, and hand of the execution to that specialized class.
		 * We also need some helper classes that do common stuff.
		 */
	}
}

JCli::getInstance('HelloYou')->execute();