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

class JBuilderComponent extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('sql'));
	}
	
	public function check()
	{
		return parent::check();
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' COMPONENT...');
		$this->out(str_repeat('-', 79));
		
		if(is_dir($this->joomlafolder.'administrator/components/'.$this->name.'/')) {
			$this->out('['.$this->name.'] Found administrator files');
			JFolder::create($this->buildfolder.'admin');
			JFolder::copy($this->joomlafolder.'administrator/components/'.$this->name.'/', $this->buildfolder.'admin', '', true);
			$this->out('['.$this->name.'] Creating MD5SUM file for administrator');
			$md5admin = new JBuilderHelperMd5();
			$md5admin->setBuildFolder($this->buildfolder.'admin/');
			$md5admin->build();
		}
		
		if(is_dir($this->joomlafolder.'components/'.$this->name.'/')) {
			$this->out('['.$this->name.'] Found frontend files');
			JFolder::create($this->buildfolder.'site');
			JFolder::copy($this->joomlafolder.'components/'.$this->name.'/', $this->buildfolder.'site', '', true);
			$this->out('['.$this->name.'] Creating MD5SUM file for site');
			$md5site = new JBuilderHelperMd5();
			$md5site->setBuildFolder($this->buildfolder.'site/');
			$md5site->build();
		}
		
		$this->prepareMediaFiles();
		
		$this->prepareLanguageFiles(array('site', 'administrator'));
		
		$this->prepareSQL();
		
		$this->addIndexFiles();
		
		$manifest = $this->getManifestObject();
		
		//Here the missing options have to be set

		//Here we should save the manifest file to the disk
		JFile::write($this->buildfolder.'manifest.xml', $manifest->build());
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('COMPONENT '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}