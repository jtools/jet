<?php
class JBuilderTemplate extends JBuilderExtension
{
	static function getOptions()
	{
		return array('copyright', 'version', 'email', 'website', 'config');
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		
	}
}