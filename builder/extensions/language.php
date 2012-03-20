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
		
	}
}