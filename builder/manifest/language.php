<?php
class JBuilderManifestLanguage extends JBuilderManifestBase
{
	public function build()
	{
		$this->checkAttributes();
		$this->log('['.$this->extname.'] Creating manifest file for '.$this->extname);

		//Create Root tag
		$root = $this->createRoot();
		
		$root->setAttribute('client', $this->client);
		
		//Create Metadata tags
		$root = $this->createMetadata($root);
		
		$tag = $this->dom->createElement('tag', $this->tag);
		$root->appendChild($tag);

		if(in_array($this->client, array('both', 'site')))
		{
			$site = $this->dom->createElement('site');
			$sitefiles = $this->dom->createElement('files');
			$sitefiles->setAttribute('folder', 'site');
			$sitefiles = $this->filelist($this->buildfolder.'/site/', $sitefiles);
			$site->appendChild($sitefiles);
			$root->appendChild($site);
		}
		if(in_array($this->client, array('both', 'administrator')))
		{
			$admin = $this->dom->createElement('administration');
			$adminfiles = $this->dom->createElement('files');
			$adminfiles->setAttribute('folder', 'admin');
			$adminfiles = $this->filelist($this->buildfolder.'/administrator/', $adminfiles);
			$admin->appendChild($adminfiles);
			$root->appendChild($admin);			
		}

		//Process media tag
		$root = $this->createMedia($root);
		
		//Handle update servers
		$root = $this->createUpdatesites($root);
		
		//Save manifest.xml file
		$this->dom->appendChild($root);
		
		//For debugging
		return $this->dom->saveXML();
	}
}