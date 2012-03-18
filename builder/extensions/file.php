<?php
class JBuilderFile extends JBuilderExtension
{
	static function getOptions()
	{
		return array('copyright', 'version', 'email', 'website', 'files', 'sql');
	}

	public function check()
	{
		return true;
	}

	public function build()
	{
		
	}
}