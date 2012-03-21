<?php
class JBuilderExtension
{
	protected $options = array();
	
	protected $name = null;
	
	protected $buildfolder = null;
	
	protected $joomlafolder = null;
	
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
		return array('copyright', 'version', 'email', 'website','name');
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
	
	public function check()
	{
		if(!$this->joomlafolder) {
			throw new Exception('*FATAL ERROR*: No Joomla Folder given!');
		}
		if(!$this->buildfolder){
			$this->out('[notice] No buildfolder given, using '.$this->joomlafolder.'.build/');
			$this->buildfolder = $this->joomlafolder;
		}
		return true;
	}
	
	protected function setManifestData(JBuilderHelperManifest $manifest)
	{
		$manifest->setType($this->getType());
		$manifest->setAuthor($this->options['author']);
		$manifest->setExtName($this->name);
		$manifest->setBuildFolder($this->buildfolder);
		$manifest->setVersion($this->options['version']);
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
		$paths = array('site' => ($this->joomlafolder.'language/'), 'admin' => ($this->joomlafolder.'administrator/language/'));
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
		$this->out('['.$this->name.'] Preparing database tables and sample content');
	}
	
	protected function addIndexFiles()
	{
		$this->out('['.$this->name.'] Adding index.html files');
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
		$adapter->create($this->buildfolder.$this->name.'.zip', $files);
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