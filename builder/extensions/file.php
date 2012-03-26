<?php
class JBuilderFile extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('files', 'sql'));
	}

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
		$this->out('TRYING TO BUILD '.$this->options['name'].' FILE EXTENSION...');
		$this->out(str_repeat('-', 79));
		
		if($this->options['files']) {
			foreach($this->options['files'] as $element) {
				if($element->getName() == 'folder') {
					if(is_dir($this->joomlafolder.(string)$element)) {
						JFolder::copy($this->joomlafolder.(string)$element, $this->buildfolder.$element, '', true);
					}
				} elseif($element->getName() == 'file') {
					if(is_file($this->joomlafolder.(string)$element)) {
						JFile::copy($this->joomlafolder.(string)$element, $this->buildfolder.(string)$element);
					}
				}
			}
		}
		
		$this->prepareLanguageFiles(array('site', 'administrator'));
		
		$this->addIndexFiles();
		
		$manifest = new JBuilderHelperManifest();
		
		$manifest = $this->setManifestData($manifest);
		
		//Here the missing options have to be set

		//Here we should save the manifest file to the disk
		JFile::write($this->buildfolder.'manifest.xml', $manifest->main());
		
		$this->createPackage();
		
		$this->out(str_repeat('-', 79));
		$this->out('FILE EXTENSION '.$this->options['name'].' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));	
	}
}