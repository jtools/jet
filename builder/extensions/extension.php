<?php
class JBuilderExtension
{
	public static function getInstance($type)
	{
		$types = array(
			'components' => 'component',
			'files' => 'file',
			'languages' => 'language',
			'libraries' => 'library',
			'modules' => 'module',
			'plugins' => 'plugin',
			'templates' => 'template',
			'packages' => 'package'
		);
		
		if(!isset($types[$type])) {
			throw new Exception('Unsupported extension type');
		}
		
		$class = 'JBuilder'.$types[$type];

		$instance = new $class;
		
		return $instance;
	}	
}