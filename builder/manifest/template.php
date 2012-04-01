<?php
class JBuilderManifestTemplate extends JBuilderManifestBase
{
	protected function checkAttributes()
	{
		parent::checkAttributes();
		
		$clients = array('site', 'administrator');
		if (!isset($this->client) || !in_array($this->client, $clients)) {
			throw new Exception("Missing attribute 'client' or client not valid");
		}
	}
	
	public function build()
	{
		$this->checkAttributes();
		$this->log('['.$this->extname.'] Creating manifest file for '.$this->extname);

		//Create Root tag
		$root = $this->createRoot();
		
		$root->setAttribute('client', $this->client);
		
		//Create Metadata tags
		$root = $this->createMetadata($root);

		$exclude = array('lang', 'media');
		//Handle file section
		if(is_dir($this->buildfolder)) {
			$files = $this->dom->createElement('files');
			$files = $this->filelist($this->buildfolder, $files, $exclude);
			$root->appendChild($files);
		}

		//Process media tag
		$root = $this->createMedia($root);
		
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