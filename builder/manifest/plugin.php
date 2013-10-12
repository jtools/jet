<?php
/**
 * JET - Joomla Extension Tools
 * A Tool to build extensions out of a Joomla development environment
 *
 * @author  Hannes Papenberg - hackwar - 02/2012
 * @version 0.1
 * @license GPL SA
 * @link    https://github.com/jtools/jet
 */

class JBuilderManifestPlugin extends JBuilderManifestBase
{
	protected function checkAttributes()
	{
		parent::checkAttributes();

		if (!isset($this->folder))
		{
			throw new Exception("Missing attribute 'folder'");
		}
	}

	public function build()
	{
		$this->checkAttributes();
		$this->log('[' . $this->extname . '] Creating manifest file for ' . $this->extname);

		//Create Root tag
		$root = $this->createRoot();

		$root->setAttribute('group', $this->folder);

		//Create Metadata tags
		$root = $this->createMetadata($root);

		$exclude = array('lang', 'media');
		$parts   = explode('_', $this->extname, 3);
		$added   = false;
		//Handle file section
		if (is_dir($this->buildfolder))
		{
			$files = $this->dom->createElement('files');
			$files = $this->filelist($this->buildfolder, $files, $exclude);
			$files->firstChild->setAttribute('plugin', $parts[2]);
			$root->appendChild($files);
		}

		if (isset($this->options['config']) && is_object($this->options['config']))
		{
			$configs = $this->options['config']->children();
			$config  = $this->dom->createElement('config');
			foreach ($configs as $c)
			{
				$temp = $this->dom->importNode(dom_import_simplexml($c), true);
				$config->appendChild($temp);
			}
			$root->appendChild($config);
		}

		//Process media tag
		$root = $this->createMedia($root);

		//Adding a script file if present for the supported extensions
		$root = $this->createScriptFile($root);

		//Create SQL install,uninstall and update tags
		$root = $this->createSQL($root);

		//Create language tag
		$root = $this->createLanguage($root);

		//Handle update servers
		$root = $this->createUpdatesites($root);

		//Save manifest.xml file
		$this->dom->appendChild($root);

		//For debugging
		return $this->dom->saveXML();
	}
}