<?php
class JBuilderExtension
{
	public static function getInstance($type)
	{
		$type = substr($type, 0, -1);
		
		$class = 'JBuilder'.$type;

		$instance = new $class;
		
		return $instance;
	}	
}