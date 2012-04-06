<?php
/**
* JET - Joomla Extension Tools
*
* A Tool to build extensions out of a Joomla development environment
*
* @author Hannes Papenberg - hackwar - 02/2012
* @version 0.1
* @license GPL SA
* @link https://github.com/jtools/jet
*/

class JBuilderLibrary extends JBuilderExtension
{
	/**
	 * Provide a list of all options that can be used by this Extension type
	 */
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('files'));
	}
	
	/**
	 * Check if all necessary options have been set and
	 * then create the necessary build folder, setting 
	 * $this->buildfolder to the correct location
	 */
	public function check()
	{
		$requiredOptions = array('files');
		$missing = array_diff($requiredOptions, array_keys($this->options));
		if(count($missing) > 0) {
			$this->out('['.$this->name.'] ERROR: The following basic options are missing: '.implode(', ', $missing));
			throw new Exception('*FATAL ERROR* Missing options!');
		}
		
		return parent::check();
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' LIBRARY...');
		$this->out(str_repeat('-', 79));
		
		if(is_dir($this->joomlafolder.'libraries/'.$this->name.'/')) {
			$this->out('['.$this->name.'] Found library files');
			JFolder::create($this->buildfolder.$this->name);
			JFolder::copy($this->joomlafolder.'libraries/'.$this->name.'/', $this->buildfolder, '', true);
			$this->out('['.$this->name.'] Creating MD5SUM file');
			$md5 = new JBuilderHelperMd5();
			$md5->setBuildFolder($this->buildfolder);
			$md5->build();
		}
		
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles(array('site', 'administrator'));
		
		$this->addIndexFiles();
		
		$manifest = $this->getManifestObject();
		
		//Here the missing options have to be set

		//Here we should save the manifest file to the disk
		JFile::write($this->buildfolder.'manifest.xml', $manifest->build());
		
		$this->createPackage('lib_'.$this->name.'.v'.$this->options['version'].'.zip');
		
		$this->out(str_repeat('-', 79));
		$this->out('LIBRARY '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}