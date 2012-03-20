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
		
	}
}