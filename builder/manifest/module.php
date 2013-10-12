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

class JBuilderManifestModule extends JBuilderManifestBase
{
	protected function checkAttributes()
	{
		parent::checkAttributes();

		$clients = array('site', 'administrator');
		if (!isset($this->client) || !in_array($this->client, $clients))
		{
			throw new Exception("Missing attribute 'client' or client not valid");
		}
	}

	public function build()
	{
		$this->checkAttributes();
		$this->log('[' . $this->extname . '] Creating manifest file for ' . $this->extname);

		//Create Root tag
		$root = $this->createRoot();

		$root->setAttribute('client', $this->client);

		//Create Metadata tags
		$root = $this->createMetadata($root);

		$exclude = array('lang', 'media');
		//Handle file section
		if (is_dir($this->buildfolder))
		{
			$files = $this->dom->createElement('files');
			$files = $this->filelist($this->buildfolder, $files, $exclude);
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

		//Adding a scriptfile if present for the supported extensions
		$root = $this->createScriptfile($root);

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