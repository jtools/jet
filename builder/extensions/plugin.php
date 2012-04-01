<?php
class JBuilderPlugin extends JBuilderExtension
{
	protected $folder = null;
	
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('sql', 'config', 'folder'));
	}
	
	public function check()
	{
		return parent::check();
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD '.$this->options['name'].' PLUGIN...');
		$this->out(str_repeat('-', 79));
		
		$parts = explode('_', $this->name, 3);
		if(is_dir($this->joomlafolder.'plugins/'.$parts[1].'/'.$parts[2].'/')) {
			$this->out('['.$this->name.'] Found frontend files');
			JFolder::copy($this->joomlafolder.'plugins/'.$parts[1].'/'.$parts[2].'/', $this->buildfolder, '', true);
			$this->out('['.$this->name.'] Creating MD5SUM file');
			$md5 = new JBuilderHelperMd5();
			$md5->setBuildFolder($this->buildfolder);
			$md5->build();
		}

		$this->prepareMediaFiles();

		$this->prepareLanguageFiles(array('administrator'));
		
		$this->addIndexFiles();

		$manifest = new JBuilderManifestPlugin();
		
		$manifest = $this->setManifestData($manifest);
		
		//Here the missing options have to be set
		$manifest->setFolder($this->options['folder']);
		if(isset($this->options['config'])) {
			$manifest->setOption('config', $this->options['config']);
		}
		
		//Here we should save the manifest file to the disk
		JFile::write($this->buildfolder.$parts[2].'.xml', $manifest->main());
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('PLUGIN '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}