<?php
class JBuilderManifestComponent extends JBuilderManifestBase
{
	public function build()
	{
		$this->checkAttributes();
		$this->log('['.$this->extname.'] Creating manifest file for '.$this->extname);

		//Create Root tag
		$root = $this->createRoot();
		
		//Create Metadata tags
		$root = $this->createMetadata($root);
		
		//Handle frontend file section
		if(is_dir($this->buildfolder.'/site/')) {
			$frontfiles = $this->dom->createElement('files');
			$frontfiles->setAttribute('folder', 'site');
			$frontfiles = $this->filelist($this->buildfolder.'/site/', $frontfiles);
			$root->appendChild($frontfiles);
		}
		
		//Handle admin area
		if(is_dir($this->buildfolder.'/admin/')) {
			$admin = $this->dom->createElement('administration');
			
			//Handle admin files
			$adminfiles = $this->dom->createElement('files');
			$adminfiles->setAttribute('folder', 'admin');
			$adminfiles = $this->filelist($this->buildfolder.'/admin/', $adminfiles);
			$admin->appendChild($adminfiles);
			
			$admin = $this->createLanguage($admin, '/admin');
			
			$menu = $this->dom->createElement('menu', $this->extname);
			
			$admin->appendChild($menu);
			
			$admin = $this->createLanguage($admin, '/administrator');
			
			$root->appendChild($admin);
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