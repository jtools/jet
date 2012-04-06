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

abstract class JBuilderExtension
{
	protected $options = array();
	
	protected $name = null;
	
	protected $buildfolder = null;
	
	protected $joomlafolder = null;
	
	protected $sql = null;
	
	public static function getInstance($type, $options)
	{
		$types = array(
			'component', 
			'file', 
			'language', 
			'library', 
			'module', 
			'plugin', 
			'template', 
			'package'
		);
		
		if(!in_array($type, $types)) {
			throw new Exception('Unsupported extension type');
		}
		$class = 'JBuilder'.$type;

		$instance = new $class($options);
		
		return $instance;
	}
	
	public static function getOptions()
	{
		return array('copyright', 'version', 'email', 'website','name', 'joomlaversion', 'update');
	}
	
	public function __construct($options)
	{
		if(isset($options['name'])) {
			$this->name = $options['name'];
		} else {
			throw new Exception('*'.$this->type.'* Fatal error: Name of the extension not set');
		}
		$this->options = $options;
	}
	
	public function getType()
	{
		return strtolower(str_replace('JBuilder', '', get_class($this)));
	}
	
	/**
	 * Check if all necessary options have been set and
	 * then create the necessary build folder, setting 
	 * $this->buildfolder to the correct location
	 */
	public function check()
	{
		if(!$this->joomlafolder) {
			throw new Exception('*FATAL ERROR*: No Joomla Folder given!');
		}
		if(!$this->buildfolder){
			$this->out('[notice] No buildfolder given, using '.$this->joomlafolder.'.build/');
			$this->buildfolder = $this->joomlafolder.'.build/';
		}
		
		$requiredOptions = array('name', 'author', 'copyright', 'license', 'email', 'website', 'version');
		
		$missing = array_diff($requiredOptions, array_keys($this->options));
		if(count($missing) > 0) {
			$this->out('['.$this->name.'] ERROR: The following basic options are missing: '.implode(', ', $missing));
			throw new Exception('*FATAL ERROR* Missing options!');
		}
		
		if(is_dir($this->buildfolder.$this->getType().'/'.$this->name.'/')) {
			$this->buildfolder .= $this->getType().'/'.$this->name.'.'.$this->options['client'].'/';
		} else {
			$this->buildfolder .= $this->getType().'/'.$this->name.'/';
		}
		if(!JFolder::create($this->buildfolder)) {
			throw new Exception('*FATAL ERROR* Couldn\'t create build folder! '.$this->buildfolder);
		}
		
		return true;
	}
	
	abstract function build();
	
	protected function getManifestObject()
	{
		$class = 'JBuilderManifest'.$this->getType();
		$manifest = new $class;
		$manifest->setAuthor($this->options['author']);
		$manifest->setEmail($this->options['email']);
		$manifest->setLicense($this->options['license']);
		$manifest->setCopyright($this->options['copyright']);
		$manifest->setWebsite($this->options['website']);
		$manifest->setExtName($this->name);
		$manifest->setBuildFolder($this->buildfolder);
		$manifest->setVersion($this->options['version']);
		$manifest->setJVersion($this->options['joomlaversion']);
		if(isset($this->options['update'])) {
			$manifest->setUpdate($this->options['update']);
		}
		if($this->sql) {
			$manifest->setSQL($this->sql);
		}
		if(isset($this->options['sql'])) {
			$manifest->setOption('tables', $this->options['sql']);
		}
		if(isset($this->options['newSQL'])) {
			$manifest->setOption('newSQL', true);
		}
		
		return $manifest;
	}
	
	protected function prepareMediaFiles()
	{
		if(is_dir($this->joomlafolder.'media/'.$this->name)) {
			$this->out('['.$this->name.'] Processing media files');
			JFolder::create($this->buildfolder.'media/');
			try {
				JFolder::copy($this->joomlafolder.'media/'.$this->name, $this->buildfolder.'media/', '', true);
			} catch(Exception $e) {
				//throw error
			}
		}
	}
	
	protected function prepareLanguageFiles($clients)
	{
		$clients = (array) $clients;
		$this->out('['.$this->name.'] Processing language files');
		$paths = array('site' => ($this->joomlafolder.'language/'), 'administrator' => ($this->joomlafolder.'administrator/language/'));
		
		//Prepare list of available language files
		$files = array();
		$found = false;
		foreach($clients as $client) {
			$files[$client] = array();
			foreach(JFolder::folders($paths[$client]) as $l) {
				if($l == 'overrides') {
					continue;
				}
				$files[$client][$l] = array();
				foreach(JFolder::files($paths[$client].$l.'/', $l.'.'.$this->name.'.*') as $file) {
					$files[$client][$l][$file] = $paths[$client].$l.'/'.$file;
					$found = true;
				}
				if(!count($files[$client][$l])) {
					unset($files[$client][$l]);
				}
			}
			if(!count($files[$client])) {
				unset($files[$client]);
			}
		}
		if($found) {
			JFolder::create($this->buildfolder.'lang/');
			foreach($files as $client => $langs) {
				if(count($clients) > 1) {
					$path = $this->buildfolder.'lang/'.$client.'/';
				} else {
					$path = $this->buildfolder.'lang/';
				}
				JFolder::create($path);
				foreach($langs as $lang => $files) {
					$this->out('['.$this->name.'] Found '.count($files).' files for "'.$client.'" client and language '.$lang);
					foreach($files as $file => $filepath) {
						try {
							JFile::copy($filepath, $path.$file);
						} catch(Exception $e) {
							//throw error
						}
					}
				}
			}
		}
	}
	
	protected function prepareSQL()
	{
		if(!isset($this->options['sql'])) {
			return;
		}
		$this->out('['.$this->name.'] Preparing database tables and sample content');
		
		$db = JFactory::getDBO();
		
		$exporter = new JDatabaseExporterMySQL();
		$exporter->setDbo($db);
		$tables = array();
		foreach($this->options['sql'] as $table) {
			$tables[] = (string)$table;
		}
		
		$exporter->from($tables);
		
		$this->sql = (string)$exporter;
	}
	
	protected function addIndexFiles()
	{
		$this->out('['.$this->name.'] Adding index.html files');
		$index = new JBuilderHelperIndexfiles(array('path' => $this->buildfolder, 'name' => $this->name));
		$index->execute();
		
	}
	
	protected function createPackage($filename = null)
	{
		$this->out('['.$this->name.'] Creating ZIP package');
		if(!$filename) {
			$filename = $this->name.'.v'.$this->options['version'].'.zip';
		}
		
		$adapter = JArchive::getAdapter('zip');
		
		$files = JFolder::files($this->buildfolder, null, true, true);
		$path = strtolower(realpath($this->buildfolder));
		foreach($files as &$file) {
			$f = array('name' => str_replace('//', '/', str_replace('\\', '/', str_replace($path,'',$file))));
			$f['data'] = file_get_contents($file);
			$file = $f;
		}
		$adapter->create(JPATH_BUILDFOLDER.'/'.$filename, $files);
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
		fwrite(STDOUT, $text . ($nl ? "\n" : null));

		return $this;
	}
	
	public function set($key, $value)
	{
		$this->$key = $value;
	}
	
	public function get($key)
	{
		return $this->$key;
	}
}