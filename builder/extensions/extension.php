<?php
class JExtensionBuilder
{
	public static function getInstance($type)
	{
		$type = substr($type, 0, -1);
		require_once(JPATH_BASE.'/extensions/'.$type.'.php');
		
		$class = 'JExtensionBuilder'.$type;

		$instance = new $class;
		
		return $instance;
	}	
}