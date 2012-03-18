<?php
class JBuilderPlugin extends JBuilderExtension
{
	static function getOptions()
	{
		return array('copyright', 'version', 'email', 'website', 'sql', 'config');
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		
	}
}