<?php
class JBuilderManifestFile extends JBuilderManifestBase
{
	public function build()
	{
		$this->checkAttributes();
		$this->log('['.$this->extname.'] Creating manifest file for '.$this->extname);

		//Create Root tag
		$root = $this->createRoot();
		
		//Create Metadata tags
		$root = $this->createMetadata($root);

		$exclude = array('lang', 'media');
		//Handle file section
		if(is_dir($this->buildfolder)) {
			$files = $this->dom->createElement('files');
			$files = $this->filelist($this->buildfolder, $files, $exclude);
			$root->appendChild($files);
		}
		
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