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

class JBuilderManifestComponent extends JBuilderManifestBase
{
	public function build()
	{
		$this->checkAttributes();
		$this->log('[' . $this->extname . '] Creating manifest file for ' . $this->extname);

		//Create Root tag
		$root = $this->createRoot();

		//Create Metadata tags
		$root = $this->createMetadata($root);

		//Handle frontend file section
		if (is_dir($this->buildfolder . '/site/'))
		{
			$frontFiles = $this->dom->createElement('files');
			$frontFiles->setAttribute('folder', 'site');
			$frontFiles = $this->filelist($this->buildfolder . '/site/', $frontFiles);
			$root->appendChild($frontFiles);
		}

		//Handle admin area
		if (is_dir($this->buildfolder . '/admin/'))
		{
			$admin = $this->dom->createElement('administration');

			//Handle admin files
			$adminFiles = $this->dom->createElement('files');
			$adminFiles->setAttribute('folder', 'admin');
			$adminFiles = $this->filelist($this->buildfolder . '/admin/', $adminFiles);
			$admin->appendChild($adminFiles);

			$menu = $this->dom->createElement('menu', $this->extname);

			$admin->appendChild($menu);

			$admin = $this->createLanguage($admin, true);

			$root->appendChild($admin);
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

	protected function createLanguage($root, $admin = false)
	{
		if ($admin && is_dir($this->buildfolder . 'lang/administrator/'))
		{
			return $this->createLanguageTag($root, 'lang/administrator/');
		}
		elseif (is_dir($this->buildfolder . 'lang/site/'))
		{
			return $this->createLanguageTag($root, 'lang/site/');
		}

		return $root;
	}
}