<?php
class JBuilderPackage extends JBuilderExtension
{
	static function getOptions()
	{
		return array('copyright', 'version', 'email', 'website');
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		
	}
}