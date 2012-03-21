<?php
class JBuilderPlugin extends JBuilderExtension
{
	protected $folder = null;
	
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('sql', 'config'));
	}
	
	public function check()
	{
		/**
		 * 	<!--
	The following properties have to be defined for a plugin to build successfully:
	  - Plugin name (plugin.name) "plg_system_sef"
	  - Plugin Type (plugin.type) "system"
	  - Copyright Statement (plugin.copyright) "(C) 2005 - 2011 Open Source Matters. All rights reserved."
	  - Version (plugin.version) "2.5.0"
	  - Author (project.author) "Joomla! Project"
	  - Author E-Mail (project.email) "admin@joomla.org"
	  - Author Website (project.website) "http://www.joomla.org"
	  - Joomla Folder (project.joomla-folder) "/var/www"
	Optional properties: (If not given, the shown defaults will be used)
	  - Build Folder (project.build-folder) "/var/www/.build" (default: ${project.joomla-folder}/.build)  
	  - License (project.license) "GNU General Public License version 2 or later; see LICENSE.txt" (default: GNU General Public License version 2 or later; see LICENSE.txt)
	  - Updatesite (plugin.update) "http://example.com/collection.xml" (default: none)
	These properties can be set in a properties file or handed in via a batch build. See plugin.properties for an example.
	-->

		 */
		
		return parent::check();
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' PLUGIN...');
		$this->out(str_repeat('-', 79));
		
		$parts = explode('_', $this->name, 3);
		if(is_dir($this->joomlafolder.'plugins/'.$parts[1].'/'.$parts[0].'/')) {
			$this->out('['.$this->name.'] Found frontend files');
			JFolder::copy($this->joomlafolder.'plugins/'.$parts[1].'/'.$parts[0].'/', $this->buildfolder, '', true);
		}
		
		$this->prepareMediaFiles();

		$this->prepareLanguageFiles(array('admin'));
		
		$this->addIndexFiles();

		$manifest = new JBuilderHelperManifest();
		
		$manifest = $this->setManifestData($manifest);
		
		//Here the missing options have to be set

		//Here we should save the manifest file to the disk
		$this->out($manifest->main());
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('PLUGIN '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}