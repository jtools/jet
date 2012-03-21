<?php
class JBuilderPackage extends JBuilderExtension
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
		$this->out('TRYING TO BUILD '.$this->options['name'].' PACKAGE...');
		$this->out(str_repeat('-', 79));
		
		$this->prepareLanguageFiles(array('admin'));
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('PACKAGE '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}