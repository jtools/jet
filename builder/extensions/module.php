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
		
		$paths = array('site' => $this->joomlafolder.'modules/', 'administrator' => $this->joomlafolder.'administrator/modules/');
		
		if(is_dir($paths[$this->options['client']].$this->name.'/')) {
			$this->out('['.$this->name.'] Found module files');
			JFolder::copy($paths[$this->options['client']].$this->name.'/', $this->buildfolder, '', true);
		}
		
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles($this->options['client']);

		$this->addIndexFiles();

		$manifest = new JBuilderHelperManifest();
		
		$manifest = $this->setManifestData($manifest);
		$manifest->setClient($this->options['client']);
		
		//Here the missing options have to be set
		if(isset($this->options['config'])) {
			$manifest->setOption('config', $this->options['config']);
		}

		//Here we should save the manifest file to the disk
		JFile::write($this->buildfolder.$this->name.'.xml', $manifest->main());
	
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('MODULE '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}