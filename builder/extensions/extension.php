<?php
class JBuilderExtension
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
		return array('copyright', 'version', 'email', 'website','name', 'joomlaversion');
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
		
		$this->buildfolder .= $this->getType().'/'.$this->name.'/';
		if(!JFolder::create($this->buildfolder)) {
			throw new Exception('*FATAL ERROR* Couldn\'t create build folder! '.$this->buildfolder);
		}
		
		return true;
	}
	
	protected function setManifestData(JBuilderHelperManifest $manifest)
	{
		$manifest->setType($this->getType());
		$manifest->setAuthor($this->options['author']);
		$manifest->setEmail($this->options['email']);
		$manifest->setLicense($this->options['license']);
		$manifest->setCopyright($this->options['copyright']);
		$manifest->setWebsite($this->options['website']);
		$manifest->setExtName($this->name);
		$manifest->setBuildFolder($this->buildfolder);
		$manifest->setVersion($this->options['version']);
		$manifest->setJVersion($this->options['joomlaversion']);
		if($this->sql) {
			$manifest->setSQL($this->sql);
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
		foreach($clients as $client) {
			$lang = array();
			if(count($clients) > 1)
				$path = $this->buildfolder.'language/'.$client.'/';
			else
				$path = $this->buildfolder.'language/';
			JFolder::create($path);
			foreach(JFolder::folders($paths[$client]) as $l) {
				if($l == 'overrides') {
					continue;
				}
				$lang[] = $l;
			}
			$this->out('['.$this->name.'] These languages were found for \''.$client.'\': '.implode(', ',$lang));
			foreach($lang as $l) {
				foreach(JFolder::files($paths[$client].$l.'/', $l.'.'.$this->name.'.*') as $file) {
					try {
						JFile::copy($paths[$client].$l.'/'.$file, $path.$file);
					} catch(Exception $e) {
						//throw error
					}
					$this->out('['.$this->name.'] Discovered '.$file);
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
		$db->setQuery('SELECT * from #__banners');
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
	
	protected function createPackage()
	{
		$this->out('['.$this->name.'] Creating ZIP package');
		
		$adapter = JArchive::getAdapter('zip');
		
		$files = JFolder::files($this->buildfolder, null, true, true);
		$path = strtolower(realpath($this->buildfolder));
		foreach($files as &$file) {
			$f = array('name' => str_replace('//', '/', str_replace('\\', '/', str_replace($path,'',$file))));
			$f['data'] = file_get_contents($file);
			$file = $f;
		}
		$adapter->create(JPATH_BUILDFOLDER.'/'.$this->name.'.v'.$this->options['version'].'.zip', $files);
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