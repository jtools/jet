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

class JBuilderManifestPackage extends JBuilderManifestBase
{
	public function build()
	{
		$this->checkAttributes();
		$this->log('[' . $this->extname . '] Creating manifest file for ' . $this->extname);

		//Create Root tag
		$root = $this->createRoot();

		//Create Metadata tags
		$root = $this->createMetadata($root);

		/**    $languages = array('component', 'language', 'library', 'module', 'plugin', 'template');
		if(in_array($this->type, $languages))
		{
		//Handle media file section
		if(is_dir($this->buildfolder.'/language/')) {
		$mediaFiles = $this->dom->createElement('media');
		$mediaFiles->setAttribute('destination', $this->extname);
		$mediaFiles = $this->filelist($this->buildfolder.'/media/', $mediaFiles);
		$root->appendChild($mediaFiles);
		}
		}**/

		//Adding a script file if present for the supported extensions
		$root = $this->createScriptFile($root);

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