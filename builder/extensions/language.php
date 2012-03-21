<?php
class JBuilderLanguage extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('client', 'tag'));
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' LANGUAGE...');
		$this->out(str_repeat('-', 79));
		
		$this->prepareMediaFiles();
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('LANGUAGE '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}