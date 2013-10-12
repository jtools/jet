<?php
/**
 * JET - Joomla Extension Tools
 * A Tool to build extensions out of a Joomla development environment
 *
 * @author  Hannes Papenberg - hackwar - 02/2012
 * @version 0.1
 * @license GPL SA
 * @link    https://github.com/jtools/jet
 */

class JBuilderLanguage extends JBuilderExtension
{
	static function getOptions()
	{
		return array_merge(parent::getOptions(), array('client', 'tag'));
	}

	public function check()
	{
		$requiredOptions = array('tag', 'client');
		$missing         = array_diff($requiredOptions, array_keys($this->options));
		if (count($missing) > 0)
		{
			$this->out('[' . $this->name . '] ERROR: The following basic options are missing: ' . implode(', ', $missing));
			throw new Exception('*FATAL ERROR* Missing options!');
		}

		return parent::check();
	}

	public function build()
	{
		$this->out(str_repeat('-', 79));
		$this->out('TRYING TO BUILD ' . $this->options['name'] . ' LANGUAGE...');
		$this->out(str_repeat('-', 79));

		$clients = array(
			'site'          => array('site'),
			'administrator' => array('administrator'),
			'both'          => array('site', 'administrator')
		);

		$paths = array(
			'site'          => $this->joomlafolder . 'language/',
			'administrator' => $this->joomlafolder . 'administrator/language/'
		);

		foreach ($clients[$this->options['client']] as $client)
		{
			$path = $paths[$client] . $this->name . '/';
			if (is_dir($path))
			{
				$this->out('[' . $this->name . '] Found ' . $client . ' files');
				JFolder::create($this->buildfolder . $client);
				JFolder::copy($path, $this->buildfolder . $client, '', true);
				$this->out('[' . $this->name . '] Creating MD5SUM file');
				$md5 = new JBuilderHelperMd5();
				$md5->setBuildFolder($this->buildfolder . $client . '/');
				$md5->build();
			}
		}

		$this->prepareMediaFiles();

		$this->addIndexFiles();

		$manifest = $this->getManifestObject();

		//Here the missing options have to be set
		$manifest->setClient($this->options['client']); //Setting this to 'both' temporarily for testing purposes

		//Here we should save the manifest file to the disk
		JFile::write($this->buildfolder . 'manifest.xml', $manifest->build());

		$this->createPackage('lng_' . $this->name . '.' . $this->options['client'] . '.v' . $this->options['version'] . '.zip');

		$this->out(str_repeat('-', 79));
		$this->out('LANGUAGE ' . $this->options['name'] . ' HAS BEEN SUCCESSFULLY BUILD!');
		$this->out(str_repeat('-', 79));
	}
}