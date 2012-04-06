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

class JBuilderManifestBase extends JBuilderHelperBase
{
	protected $options = array();
	protected $type = null;
	protected $extname = null;
	protected $exttitle = null;
	protected $folder = null;
	protected $buildfolder = null;
	protected $version = null;
	protected $jversion = null;
	protected $copyright = null;
	protected $author = null;
	protected $email = null;
	protected $website = null;
	protected $license = null;
	protected $update = null;
	protected $client = null;
	protected $dom = null;
	protected $sql = null;
	
	public function __construct()
	{
		$this->type = strtolower(str_replace('JBuilderManifest', '', get_class($this)));
	}
	
	public function setOption($key, $value)
	{
		$this->options[$key] = $value;
	}
	
	public function setType($type)
	{
		$this->type = $type;
	}

	public function setExtName($extname)
	{
		$this->extname = $extname;
	}

	public function setExtTitle($exttitle)
	{
		$this->exttitle = $exttitle;
	}

	public function setFolder($folder)
	{
		$this->folder = $folder;
	}

	public function setBuildFolder($buildfolder)
	{
		$this->buildfolder = $buildfolder;
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	public function setJVersion($jversion)
	{
		$this->jversion = $jversion;
	}

	public function setCopyright($data)
	{
		$this->copyright = $data;
	}

	public function setAuthor($data)
	{
		$this->author = $data;
	}

	public function setEmail($data)
	{
		$this->email = $data;
	}

	public function setWebsite($data)
	{
		$this->website = $data;
	}

	public function setLicense($data)
	{
		$this->license = $data;
	}

	public function setUpdate($data)
	{
		$this->update = $data;
	}

	public function setClient($client)
	{
		$this->client = $client;
	}
	
	public function setTag($tag)
	{
		$this->tag = $tag;
	}
	
	public function setSQL($sql)
	{
		$this->sql = $sql;
	}
	
	/**
	 * Create Root node of the manifest
	 */
	protected function createRoot()
	{
		$this->dom = new DOMDocument();
		$this->dom->encoding = 'utf-8';//set the document encoding
		$this->dom->xmlVersion = '1.0';//set xml version
		$this->dom->formatOutput = true;//Nicely formats output with indentation and extra space 
		
		$root = $this->dom->createElement('extension');
		$root->setAttribute('type', $this->type);
		$root->setAttribute('method', 'upgrade');
		$root->setAttribute('version', $this->jversion);

		return $root;		
	}
	
	/**
	 * Create the Metadata tags
	 */
	protected function createMetadata($root)
	{
		$name = 'name';
		if($this->type == 'package')
			$name = 'packagename';
		if($this->exttitle)
			$name = $this->dom->createElement($name, $this->exttitle);
		else
			$name = $this->dom->createElement($name, $this->extname);
		$root->appendChild($name);
		
		$author = $this->dom->createElement('author', $this->author);
		$creation = $this->dom->createElement('creationDate', date('F Y'));
		$copyright = $this->dom->createElement('copyright', $this->copyright);
		$license = $this->dom->createElement('license', $this->license);
		$authormail = $this->dom->createElement('authorEmail', $this->email);
		$authorurl = $this->dom->createElement('authorUrl', $this->website);
		$version = $this->dom->createElement('version', $this->version);
		$description = $this->dom->createElement('description', strtoupper($this->extname).'_EXTENSION_DESC');

		$root->appendChild($author);
		$root->appendChild($creation);
		$root->appendChild($copyright);
		$root->appendChild($license);
		$root->appendChild($authormail);
		$root->appendChild($authorurl);
		$root->appendChild($version);
		$root->appendChild($description);
		
		return $root;
	}

	/**
	 * This method generates the media tag 
	 */
	protected function createMedia($root)
	{
		//Handle media file section
		if(is_dir($this->buildfolder.'/media/')) {
			$mediafiles = $this->dom->createElement('media');
			$mediafiles->setAttribute('destination', $this->extname);
			$mediafiles = $this->filelist($this->buildfolder.'/media/', $mediafiles);
			$root->appendChild($mediafiles);
		}
		
		return $root;
	}

	/**
	 * This method generates a scriptfile tag
	 */
	protected function createScriptfile($root)
	{
		$path = $this->buildfolder;
		if($this->type == 'component') {
			$path .= '/admin';
		}
		if(is_file($path.'/'.$this->extname.'.script.php')) {
			$scripttag = $this->dom->createElement('scriptfile', $this->extname.'.script.php');
			$root->appendChild($scripttag);
		}
		
		return $root;
	}
	
	/**
	 * This method adds the necessary SQL tags
	 */
	protected function createSQL($root)
	{
		if(isset($this->options['newSQL'])) {
			if($this->sql) {
				$xml = simplexml_load_string($this->sql);
				$tables = $xml->xpath('database');
				$tables = $tables[0]->children();
				$sql = $this->dom->createElement('sql');
				foreach($tables as $table) {
					$temp = $this->dom->importNode(dom_import_simplexml($table), true);

					$sql->appendChild($temp);
				}
				$root->appendChild($sql);
			}
		} else {
			$path = $this->buildfolder;
			if($this->type == 'component')
				$path .= '/admin';
			
			if(is_dir($path.'/sql')) {
				
				if(file_exists($path.'/sql/install.mysql.utf8.sql')) {
					$install = $this->dom->createElement('install');
					$sql = $this->dom->createElement('sql');
					$folder = $path.'/sql/';
					$dir = opendir($folder);
					while(false !== ($entry = readdir($dir))) {
						if(is_file($folder.$entry) && substr($entry, 0, 7) == 'install') {
							$data = explode('.', $entry);
							$e = $this->dom->createElement('file', 'sql/'.$entry);
							$e->setAttribute('charset', 'utf8');
							$e->setAttribute('folder', 'sql');
							$e->setAttribute('driver', $data[1]);
							$sql->appendChild($e);
						}
					}
					$install->appendChild($sql);
					$root->appendChild($install);
				}
			
				if(file_exists($path.'/sql/uninstall.mysql.utf8.sql')) {
					$uninstall = $this->dom->createElement('uninstall');
					$sql = $this->dom->createElement('sql');
					$folder = $path.'/sql/';
					$dir = opendir($folder);
					while(false !== ($entry = readdir($dir))) {
						if(is_file($folder.$entry) && substr($entry, 0, 9) == 'uninstall') {
							$data = explode('.', $entry);
							$e = $this->dom->createElement('file', 'sql/'.$entry);
							$e->setAttribute('charset', 'utf8');
							$e->setAttribute('folder', 'sql');
							$e->setAttribute('driver', $data[1]);
							$sql->appendChild($e);
						}
					}
					$uninstall->appendChild($sql);
					$root->appendChild($uninstall);
				}
			
				if(is_dir($path.'/sql/updates')) {
					$update = $this->dom->createElement('update');
					$schemas = $this->dom->createElement('schemas');
					$folder = $path.'/sql/updates/';
					$dir = opendir($folder);
					while(false !== ($entry = readdir($dir))) {
						if($entry == '.' || $entry == '..') {
							continue;
						}
						if(is_dir($folder.$entry)) {
							$e = $this->dom->createElement('schemapath', 'sql/updates/'.$entry);
							$e->setAttribute('type', $entry);
							$schemas->appendChild($e);
						}
					}
					$update->appendChild($schemas);
					$root->appendChild($update);
				}
			}
		}
		if(isset($this->options['tables'])) {
			$tables = $this->dom->createElement('tables');
			foreach($this->options['tables'] as $table) {
				$t = $this->dom->createElement('table', (string)$table);
				$attr = $table->attributes();
				if(isset($attr['optional'])) {
					$t->setAttribute('type', 'optional');
				}
				$tables->appendChild($t);
			}
			$root->appendChild($tables);
		}
		
		return $root;
	}
	
	/**
	 * This method handles the updatesites tags
	 */
	protected function createUpdatesites($root)
	{
		$updateSites = explode(',', $this->update);
		if(count($updateSites) && strlen($updateSites[0])) {
			$updates = $this->dom->createElement('updateservers');
			$i = 1;
			foreach($updateSites as $updateSite) {
				$server = $this->dom->createElement('server', $updateSite);
				$server->setAttribute('type', 'extension');
				$server->setAttribute('priority', $i);
				$server->setAttribute('name', $this->extname);
				$updates->appendChild($server);
			}
			$root->appendChild($updates);
		}
		
		return $root;
	}
	
	/**
	 * This method handles the language tags
	 */
	protected function createLanguage($root)
	{
		if(is_dir($this->buildfolder.'lang/')) {
			return $this->createLanguageTag($root, 'lang/');
		}
		
		return $root;
	}
	
	protected function createLanguageTag($root, $folder)
	{
		$lang = $this->dom->createElement('languages');
		$dir = opendir($this->buildfolder.$folder);
		while(false !== ($entry = readdir($dir))) {
			if($entry == '.' || $entry == '..')
				continue;
			if(is_file($this->buildfolder.$folder.$entry) && $entry != 'index.html') {
				$tag = explode('.', $entry);
				$e = $this->dom->createElement('language', $folder.$entry);
				$e->setAttribute('tag', $tag[0]);
				$lang->appendChild($e);
			}
		}
		$root->appendChild($lang);

		return $root;
	}

	protected function filelist($folder, $dom, $exclude = array())
	{
		if(!is_dir($folder)) {
			return;
		}
		$files = array();
		$folders = array();
		$dir = opendir($folder);
		while(false !== ($entry = readdir($dir))) {
			$e = null;
			if(in_array($entry, $exclude)) {
				continue;
			}
			if(is_file($folder.$entry)) {
				$files[] = $entry;
			} elseif(is_dir($folder.$entry) && $entry != '.' && $entry != '..') {
				$folders[] = $entry;
			}
		}
		sort($files);
		sort($folders);
		
		foreach($files as $file) {
			$e = $this->dom->createElement('file', $file);
			$dom->appendChild($e);
		}
		
		foreach($folders as $folder) {
			$e = $this->dom->createElement('folder', $folder);
			$dom->appendChild($e);
		}
		
		return $dom;
	}

	protected function checkAttributes()
	{
		if (!isset($this->type)) {
			throw new Exception("Missing attribute 'type'");
		}
		
		if (!isset($this->extname)) {
			throw new Exception("Missing attribute 'extname'");
		}
		
		if (!isset($this->buildfolder)) {
			throw new Exception("Missing attribute 'buildfolder'");
		}
		
		if (!isset($this->version)) {
			throw new Exception("Missing attribute 'version'");
		}
	}
}