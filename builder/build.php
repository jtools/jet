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
	
	protected $options = array();
	
	public function execute()
	{
		JLoader::discover('JBuilderHelper', JPATH_BASE.'/helpers/');
		JLoader::discover('JBuilder', JPATH_BASE.'/extensions/');

		$this->out(str_repeat('=', 79));
		$text = 'JOOMLA! EXTENSION TOOLS';
		$this->out(str_repeat(' ', (79 - strlen($text))/2).$text);
		$text = 'Extension Builder';
		$this->out(str_repeat(' ', (79 - strlen($text))/2).$text);
		$this->out(str_repeat('=', 79));

		$this->loadPropertiesFromCLI();
		
		if(count($this->input->args)) {
			$this->loadPropertiesFromFile($this->input->args[0]);
		} else {
			$this->loadPropertiesFromInput();
		}
		
		$types = array();
		
		foreach($this->extensions as $extension) {
			if(is_bool($extension->check())) {
				$extension->build();
			} else {
				//throw error
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

		if($common = $xml->xpath('common'))
		{
			$allowedGlobalOptions = array('copyright', 'version', 'email', 'website');
			foreach($common[0]->children() as $option) {
				if(in_array($option->getName(), $allowedGlobalOptions)) {
					$this->options[$option->getName()] = (string) $option;
				}
			}
		}

		$types = array(
			'components' => 'component',
			'files' => 'file',
			'languages' => 'language',
			'libraries' => 'library',
			'modules' => 'module',
			'plugins' => 'plugin',
			'templates' => 'template',
			'packages' => 'package'
		);

		foreach($types as $tag => $type) {
			$extensions = $xml->xpath($tag.'/'.$type);
			if(count($extensions) == 0) {
				continue;
			}
			
			$options = call_user_func(array('JBuilder'.$type, 'getOptions'));
			
			$opts = $this->options;
			foreach($extensions as $extension) {
				foreach($extension->children() as $exopt) {
					if(!in_array($exopt->getName(), $options)) {
						continue;
					}
					
					if($exopt->count()) {
						$opts[$exopt->getName()] = $exopt->children();
					} elseif(count($exopt->attributes())) {
						$opts[$exopt->getName()] = $exopt->attributes();
					} else {
						$opts[$exopt->getName()] = (string) $exopt;
					}
				}
				$this->extensions[] = JBuilderExtension::getInstance($type, $opts);
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