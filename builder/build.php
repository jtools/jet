<?php
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__));

require_once '../libraries/import.php';

jimport('joomla.application.cli');

class JoomlaExtensionBuilder extends JCli
{
	protected $extensions = array();
	
	protected $types = array();
	
	protected $verbose = false;
	
	public function execute()
	{
		JLoader::register('JBuilderBase', JPATH_BASE.'/helpers/base.php');
		JLoader::register('JIndexFiles', JPATH_BASE.'/helpers/indexfiles.php');
		JLoader::register('JManifest', JPATH_BASE.'/helpers/manifest.php');
		JLoader::register('JExtensionBuilder', JPATH_BASE.'/extensions/extension.php');

		$this->out(str_repeat('=', 79));
		$text = 'JOOMLA! EXTENSION TOOLS';
		$this->out(str_repeat(' ', (79 - strlen($text))/2).$text);
		$text = 'Extension Builder';
		$this->out(str_repeat(' ', (79 - strlen($text))/2).$text);
		$this->out(str_repeat('=', 79));

		if(count($this->input->args)) {
			$this->loadPropertiesFromFile($this->input->args[0]);
		} else {
			$this->loadPropertiesFromInput();
		}
		
		$this->loadPropertiesFromCLI();
		
		$types = array();
		
		foreach($this->types as $type) {
			$types[$type] = 0;
			foreach($this->extensions[$type] as $extension) {
				$adapter = JExtensionBuilder::getInstance($type, $extension);
		
				$adapter->build();
				$types[$type]++;
			}
		}
		
		$this->out('FINISHED!!');
		$this->out('Statistics:');
		foreach($types as $type => $count) {
			$this->out($type.': '.$count);
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
		
		$xml = JFactory::getXML($path, true);
		
		if(!$xml) {
			$this->out('[config] Not a valid XML file');
			$this->close(1);
		}
		
		if($xml->getName() != 'jet') {
			$this->out('[config] Not a valid JET file');
			$this->close(1);
		}
		
		foreach($xml->children() as $child) {
			$this->types[] = $child->getName();
			$this->extensions[$child->getName()] = array();
			
			foreach($child->children() as $extension) {
				$this->extensions[$child->getName()][] = $extension;
			}
		}
		
	}

	protected function loadPropertiesFromInput()
	{
		$this->out('Not supported yet');
		$this->close(1);
	}

	protected function loadPropertiesFromCLI()
	{
		if($this->input->get('v') || $this->input->get('verbose')) {
			$this->verbose = true;
			$this->out('[config] verbose mode enabled');
		}
	}
}

JCli::getInstance('JoomlaExtensionBuilder')->execute();