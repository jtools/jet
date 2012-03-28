<?php
class JBuilderTemplate extends JBuilderExtension
{
	protected $client = 'site';
	
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('config', 'client'));
	}
	
	public function check()
	{
		$requiredOptions = array('client');
		$missing = array_diff($requiredOptions, array_keys($this->options));
		if(count($missing) > 0) {
			$this->out('['.$this->name.'] ERROR: The following basic options are missing: '.implode(', ', $missing));
			throw new Exception('*FATAL ERROR* Missing options!');
		}
		
		return parent::check();
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' TEMPLATE...');
		$this->out(str_repeat('-', 79));
		
		if(is_dir($this->joomlafolder.'templates/'.$this->name.'/')) {
			$this->out('['.$this->name.'] Found template files');
			JFolder::copy($this->joomlafolder.'templates/'.$this->name.'/', $this->buildfolder, '', true);
		}
		
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles(array($this->client));
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		
		$manifest = $this->setManifestData($manifest);
		
		//Here the missing options have to be set
		$manifest->setClient($this->options['client']);

		//Here we should save the manifest file to the disk
		JFile::write($this->buildfolder.'templateDetails.xml', $manifest->main());
		
		$this->createPackage('tpl_'.$this->name.'.'.$this->options['client'].'.v'.$this->options['version'].'.zip');
		
		$this->out(str_repeat('-', 79));
		$this->out('TEMPLATE '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}