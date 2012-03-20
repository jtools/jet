<?php
class JBuilderTemplate extends JBuilderExtension
{
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
		
	}
}