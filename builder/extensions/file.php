<?php
class JBuilderFile extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('files', 'sql'));
	}

	public function check()
	{
		return true;
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' FILE EXTENSION...');
		$this->out(str_repeat('-', 79));
		
		$this->prepareLanguageFiles(array('site', 'admin'));
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('FILE EXTENSION '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));	
	}
}