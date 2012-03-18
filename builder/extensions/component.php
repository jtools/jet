<?php
class JBuilderComponent extends JBuilderExtension
{
	static function getOptions()
	{
		return array('copyright', 'version', 'email', 'website', 'sql');
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		
	}
}