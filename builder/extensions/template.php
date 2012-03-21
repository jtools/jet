<?php
class JBuilderTemplate extends JBuilderExtension
{
	protected $client = 'site';
	
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('config'));
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' TEMPLATE...');
		$this->out(str_repeat('-', 79));
		
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles(array($this->client));
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('TEMPLATE '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}