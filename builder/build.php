<?php
define( '_JEXEC', 1 );
define('JPATH_BASE', dirname(__FILE__));
define('JPATH_ROOT', realpath(JPATH_BASE.'/../'));

$GLOBALS['timer'] = explode(' ', microtime());

require_once '../libraries/import.php';

class JoomlaExtensionBuilder extends JApplicationCli
{
	protected $extensions = array();

	protected $types = array();

	protected $verbose = false;

	protected $options = array();

	protected $buildfolder = null;

	protected $joomlafolder = null;

	protected function doExecute()
	{
		JLoader::discover('JBuilderHelper', JPATH_BASE.'/helpers/');
		JLoader::discover('JBuilder', JPATH_BASE.'/extensions/');
		JLoader::discover('J', JPATH_BASE.'/../libraries/joomla/filesystem/');

		$this->out(str_repeat('=', 79));
		$this->out('JOOMLA! EXTENSION TOOLS',true,true);
		$this->out('Extension Builder',true,true);
		$this->out(str_repeat('=', 79));
		
		$this->loadPropertiesFromCLI();

		if(count($this->input->args)) {
			$this->loadPropertiesFromFile($this->input->args[0]);
		} else {
			$this->loadPropertiesFromInput();
		}
		
		$config = JFactory::getConfig($this->joomlafolder.'configuration.php');

		if(!$this->joomlafolder) {
			$this->out('*FATAL ERROR* No Joomla installation as build source given!');
			$this->close(1);
		}

		$this->cleanBuildFolder();

		$types = array();
		define('JPATH_BUILDFOLDER', $this->buildfolder);
		foreach($this->extensions as $extension) {
			$folder = $this->isFolderPrepared($extension->getType(), $extension);
			try {
				$extension->check();
				$extension->build();
			} catch(Exception $e) {
				$this->out($e->getMessage());
				$this->close(1);
			}
		}

		$this->out('FINISHED!!');
		$this->out('Statistics:');
		foreach($types as $type => $count) {
			$this->out($type.': '.$count);
		}

		$time = explode(' ', microtime());
		$this->out('Time elapsed: '.(($time[0] - $GLOBALS['timer'][0]) + (float) ($time[1] - $GLOBALS['timer'][1])));

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
			$allowedGlobalOptions = array('name', 'type', 'client');
			foreach($common[0]->children() as $option) {
				if(!in_array($option->getName(), $allowedGlobalOptions)) {
					$this->options[$option->getName()] = (string) $option;
				}
				if($option->getName() == 'joomla' && !$this->joomlafolder && is_dir((string) $option)) {
					$this->joomlafolder = (string) $option;
				}
			}
		}

		if(!$this->buildfolder) {
			$this->buildfolder = $this->joomlafolder.'.build/';
			if(!is_dir($this->buildfolder)) {
				JFolder::create($this->buildfolder);
				$this->out('[info] Creating '.$this->buildfolder.' for building');
			} else {
				$this->out('[info] Using '.$this->buildfolder.' for building');
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

			foreach($extensions as $extension) {
				$opts = $this->options;
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
				$adapter = JBuilderExtension::getInstance($type, $opts);

				$adapter->set('joomlafolder', $this->joomlafolder);
				$adapter->set('buildfolder', $this->buildfolder);

				$this->extensions[] = $adapter;
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

		if($this->input->get('j')) {
			$this->joomlafolder = $this->input->get('j', null, 'STRING');
		} elseif($this->input->get('joomla')) {
			$this->joomlafolder = $this->input->get('joomla', null, 'STRING');
		}
		if($this->joomlafolder) {
			if(is_dir($this->joomlafolder)){
				$this->out('[info] Using '.$this->joomlafolder.' as build source');
			} else {
				$this->out('*FATAL ERROR* Given folder for Joomla installation is not reachable!');
				$this->close(1);
			}
		}

		if($this->input->get('b')) {
			$this->buildfolder = $this->input->get('b', null, 'STRING');
		} elseif($this->input->get('build')) {
			$this->buildfolder = $this->input->get('build', null, 'STRING');
		}
		
		if($this->input->get('n')) {
			$this->options['newSQL'] = true;
		}

		if($this->buildfolder) {
			if(is_dir($this->buildfolder)){
				$this->out('[info] Using '.$this->buildfolder.' for building');
				define('JPATH_BUILDFOLDER', $this->buildfolder);
			} else {
				if(!JFolder::create($this->buildfolder)) {
					$this->out('*FATAL ERROR* Given folder for building is not reachable!');
					$this->close(1);
				} else {
					$this->out('[info] Creating '.$this->buildfolder.' for building');
					define('JPATH_BUILDFOLDER', $this->buildfolder);
				}
			}
		}
	}

	protected function isFolderPrepared($type, $extension)
	{
		if(!is_dir($this->buildfolder.$type.'/'))
		{
			JFolder::create($this->buildfolder.$type.'/');
		}
		return true;
	}

	protected function cleanBuildFolder()
	{
		$this->out('Cleaning up the build folder...');
		$folders = JFolder::folders($this->buildfolder, '.', false, true);
		if(count($folders)) {
			$count = 0;
			foreach($folders as $folder) {
				if(!JFolder::delete($folder)) {
					throw new Exception('Folder '.$folder.' could not be deleted!');
				}
				$count++;
			}
			$this->out('Deleted '.$count.' folders');
		} else {
			$this->out('No folders to delete');
		}
	}

	/**
	 * Write a string to standard output.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  JApplicationCli  Instance of $this to allow chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   11.1
	 */
	public function out($text = '', $nl = true, $center = false)
	{
		if($center) $text = str_repeat(' ', (79 - strlen($text))/2).$text;

		return parent::out($text, $nl);
	}
}

JApplicationCli::getInstance('JoomlaExtensionBuilder')->execute();
