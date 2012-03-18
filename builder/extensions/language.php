<?php
class JBuilderLanguage extends JBuilderExtension
{
	static function getOptions()
	{
		return array('copyright', 'version', 'email', 'website', 'client', 'tag');
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		
	}
}