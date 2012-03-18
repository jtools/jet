<?php
class JBuilderExtension
{
	protected $options = array();
	
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
		$class = 'JBuilder'.$type;

		$instance = new $class($options);
		
		return $instance;
	}
	
	public static function getOptions()
	{
		return array('name');
	}
	
	public function __construct($options)
	{
		$this->options = $options;
	}
	
	public function getType()
	{
		return strtolower(str_replace('JBuilder', '', get_class($this)));
	}
	
	protected function getManifestData()
	{
		return array();
	}
	
	protected function prepareMediaFiles()
	{
		
	}
	
	protected function prepareLanguageFiles()
	{
		
	}
	
	/**
	 * Write a string to standard output.
	 *
	 * @param   string   $text  The text to display.
	 * @param   boolean  $nl    True (default) to append a new line at the end of the output string.
	 *
	 * @return  JApplicationCli  Instance of $this to allow chaining.
	 *
	 * @codeCoverageIgnore
	 * @since   11.1
	 */
	public function out($text = '', $nl = true, $center = false)
	{
		if($center) $text = str_repeat(' ', (79 - strlen($text))/2).$text;
		fwrite(STDOUT, $text . ($nl ? "\n" : null));

		return $this;
	}
}