<?php
class JBuilderModule extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('sql', 'config', 'client'));
	}
	
	public function check()
	{
		/**
		 * 	<!--
	The following properties have to be defined for a module to build successfully:
	  - Module name (module.name) "mod_menu"
	  - Client (module.client) "site" or "admin"
	  - Copyright Statement (module.copyright) "(C) 2005 - 2011 Open Source Matters. All rights reserved."
	  - Version (module.version) "2.5.0"
	  - Author (project.author) "Joomla! Project"
	  - Author E-Mail (project.email) "admin@joomla.org"
	  - Author Website (project.website) "http://www.joomla.org"
	  - Joomla Folder (project.joomla-folder) "/var/www"
	Optional properties: (If not given, the shown defaults will be used)
	  - Build Folder (project.build-folder) "/var/www/.build" (default: ${project.joomla-folder}/.build)  
	  - License (project.license) "GNU General Public License version 2 or later; see LICENSE.txt" (default: GNU General Public License version 2 or later; see LICENSE.txt)
	  - Updatesite (module.update) "http://example.com/collection.xml" (default: none)
	These properties can be set in a properties file or handed in via a batch build. See component.properties for an example.
	-->

		 */
		return parent::check();;
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' MODULE...');
		$this->out(str_repeat('-', 79));
		
/**
		<if>
			<equals arg1="${module.client}" arg2="site" />
			<then>
				<property name="module.folder" value="${project.joomla-folder}/modules/${module.name}" />
			</then>
			<else>
				<property name="module.folder" value="${project.joomla-folder}/administrator/modules/${module.name}" />
			</else>
		</if>
		<echo msg="Creating folder for the module." /> 
		<mkdir dir="${project.build-folder}/modules/${module.client}/${module.name}" />
		<echo msg="Copy the files for the module." />
		<copy todir="${project.build-folder}/modules/${module.client}/${module.name}">
			<fileset dir="${module.folder}">
				<include name="**" />
				<exclude name="${module.name}.xml" />
			</fileset>
		</copy>
		<echo msg="----------------------------------------" />
**/
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles($this->options['client']);

		$this->addIndexFiles();

		$manifest = new JBuilderHelperManifest();
		
		$manifest = $this->setManifestData($manifest);
		
		//Here the missing options have to be set

		//Here we should save the manifest file to the disk
		$this->out($manifest->main());
	
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('MODULE '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}