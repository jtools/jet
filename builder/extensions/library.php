<?php
class JBuilderLibrary extends JBuilderExtension
{
	static function getOptions()
	{
		return parent::getOptions();
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' LIBRARY...');
		$this->out(str_repeat('-', 79));
		
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles(array('site', 'admin'));
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('LIBRARY '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}