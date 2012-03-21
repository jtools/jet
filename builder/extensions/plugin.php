<?php
class JBuilderPlugin extends JBuilderExtension
{
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
		
		return true;
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' PLUGIN...');
		$this->out(str_repeat('-', 79));
		
		/**
		<echo msg="Creating folder for the plugin." /> 
		<mkdir dir="${project.build-folder}/plugins/${plugin.client}/${plugin.name}" />
		<echo msg="Copy the files for the plugin." />
		<copy todir="${project.build-folder}/plugins/${plugin.client}/${plugin.name}">
			<fileset dir="${plugin.folder}">
				<include name="**" />
				<exclude name="${plugin.name}.xml" />
			</fileset>
		</copy>
		<echo msg="----------------------------------------" />
		**/
		
		$this->prepareMediaFiles();

		$this->prepareLanguageFiles(array('admin'));
		
		$this->addIndexFiles();
		/**		
		<!-- Creating manifest file -->
		<echo msg="Creating manifest file" />
		<joomlamanifest 
			type="plugin" 
			extname="${plugin.name}" 
			buildfolder="${project.build-folder}/plugins/${plugin.client}/${plugin.name}" 
			version="${plugin.version}"
			copyright="${plugin.copyright}"
			author="${project.author}"
			email="${project.email}"
			website="${project.website}"
			license="${project.license}"
			update="${plugin.update}"
		/>
		<echo msg="Manifest file created!" />
		<echo msg="----------------------------------------" />
		 */
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('PLUGIN '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}