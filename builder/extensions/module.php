<?php
class JBuilderModule extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('sql', 'config', 'client'));
	}
	
	public function check()
	{
		$requiredOptions = array('client');
		$missing = array_diff($requiredOptions, array_keys($this->options));
		if(count($missing) > 0) {
			$this->out('['.$this->name.'] ERROR: The following basic options are missing: '.implode(', ', $missing));
			throw new Exception('*FATAL ERROR* Missing options!');
		}
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
		$manifest->setClient($this->options['client']);
		
		//Here the missing options have to be set

		//Here we should save the manifest file to the disk
		JFile::write($this->buildfolder.'manifest.xml', $manifest->main());
	
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('MODULE '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}