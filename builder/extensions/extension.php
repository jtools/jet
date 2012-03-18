<?php
class JBuilderExtension
{
	public static function getInstance($type, $options)
	{
		$types = array(
			'component', 
			'file', 
			'language', 
			'library', 
			'module', 
			'plugin', 
			'template', 
			'package'
		);
		
		if(!in_array($type, $types)) {
			throw new Exception('Unsupported extension type');
		}
		var_dump($options);
		$class = 'JBuilder'.$type;

		$instance = new $class($options);
		
		return $instance;
	}	
}