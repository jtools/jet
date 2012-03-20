<?php
class JBuilderPlugin extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('sql', 'config'));
	}
	
	public function check()
	{
		return true;
	}

	public function build()
	{
		
	}
}