<?php
class JBuilderComponent extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('sql'));
	}
	
	public function check()
	{
		/**
		 * 	The following properties have to be defined for a component to build successfully:
	  - Component name (component.name) "com_content"
	  - Copyright Statement (component.copyright) "(C) 2005 - 2011 Open Source Matters. All rights reserved."
	  - Version (component.version) "2.5.0"
	  - Author (project.author) "Joomla! Project"
	  - Author E-Mail (project.email) "admin@joomla.org"
	  - Author Website (project.website) "http://www.joomla.org"
	  - Joomla Folder (project.joomla-folder) "/var/www"
	Optional properties: (If not given, the shown defaults will be used)
	  - Build Folder (project.build-folder) "/var/www/.build" (default: ${project.joomla-folder}/.build)  
	  - License (project.license) "GNU General Public License version 2 or later; see LICENSE.txt" (default: GNU General Public License version 2 or later; see LICENSE.txt)
	  - Updatesite (component.update) "http://example.com/collection.xml" (default: none)
	These properties can be set in a properties file or handed in via a batch build. See component.properties for an example.
	-->

		 */
		return parent::check();
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' COMPONENT...');
		$this->out(str_repeat('-', 79));
		
		if(is_dir($this->joomlafolder.'administrator/components/'.$this->name.'/')) {
			$this->out('['.$this->name.'] Found administrator files');
			JFolder::create($this->buildfolder.'admin');
			JFolder::copy($this->joomlafolder.'administrator/components/'.$this->name.'/', $this->buildfolder.'admin', '', true);
		}
		
		if(is_dir($this->joomlafolder.'components/'.$this->name.'/')) {
			$this->out('['.$this->name.'] Found frontend files');
			JFolder::create($this->buildfolder.'site');
			JFolder::copy($this->joomlafolder.'components/'.$this->name.'/', $this->buildfolder.'site', '', true);
		}
		
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles(array('site', 'admin'));
		
		$this->prepareSQL();
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		
		$manifest = $this->setManifestData($manifest);
		
		//Here the missing options have to be set

		//Here we should save the manifest file to the disk
		$this->out($manifest->main());
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('COMPONENT '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}