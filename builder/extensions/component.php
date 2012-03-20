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
		
		/**
	<!-- This target builds a Joomla component package-->
	<target name="component-build" depends="component-build-prepare">
	
		<!-- Processing backend component part -->
		<if>
			<available file="${project.joomla-folder}/administrator/components/${component.name}" type="dir" />
			<then>
				<echo msg="Found a backend component part!" />
				<property name="component.admin" value="true" />
				<echo msg="Creating folder for backend component part." /> 
				<mkdir dir="${project.build-folder}/components/${component.name}/admin" />
				<echo msg="Copy the files for backend component part." />
				<copy todir="${project.build-folder}/components/${component.name}/admin">
					<fileset dir="${project.joomla-folder}/administrator/components/${component.name}">
						<include name="**" />
						<exclude name="manifest.xml" />
					</fileset>
				</copy>
				<echo msg="----------------------------------------" />
			</then>
			<else>
				<property name="component.admin" value="false" />
			</else>
		</if>
		
		<!-- Processing frontend component part -->
		<if>
			<available file="${project.joomla-folder}/components/${component.name}" type="dir" />
			<then>
				<echo msg="Found a frontend component part!" />
				<property name="component.front" value="true" />
				<echo msg="Creating folder for frontend component part." /> 
				<mkdir dir="${project.build-folder}/components/${component.name}/front" />
				<echo msg="Copy the files for frontend component part." />
				<copy todir="${project.build-folder}/components/${component.name}/front">
					<fileset dir="${project.joomla-folder}/components/${component.name}">
						<include name="**" />
					</fileset>
				</copy>
				<echo msg="----------------------------------------" />
			</then>
			<else>
				<property name="component.front" value="false" />
			</else>
		</if>
**/
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles(array('site', 'admin'));
		
		$this->prepareSQL();
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		/**
		<!-- Creating manifest file -->
		<echo msg="Creating manifest file" />
		<joomlamanifest 
			type="component" 
			extname="${component.name}" 
			buildfolder="${project.build-folder}/components/${component.name}" 
			version="${component.version}"
			copyright="${component.copyright}"
			author="${project.author}"
			email="${project.email}"
			website="${project.website}"
			license="${project.license}"
			update="${component.update}"
		/>
		<echo msg="Manifest file created!" />
		<echo msg="----------------------------------------" />
		 */
		
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('COMPONENT '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}