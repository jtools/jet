<?php
class JBuilderComponent extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('sql'));
	}
	
	public function check()
	{
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
		
		$this->prepareLanguageFiles(array('site', 'administrator'));
		
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