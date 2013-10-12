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

class JBuilderHelperMd5 extends JBuilderHelperBase
{
	protected $md5s = array();

	protected $buildfolder = null;

	public function setBuildFolder($folder)
	{
		$this->buildfolder = $folder;
	}

	public function build()
	{
		$this->readDir('');

		if (count($this->md5s))
		{
			$out = '';
			foreach ($this->md5s as $file => $md5)
			{
				$out .= $md5 . '  ' . $file . "\n";
			}
			JFile::write($this->buildfolder . 'MD5SUM', $out);
		}
	}

	protected function readDir($dir)
	{
		if ($dh = opendir($this->buildfolder . $dir))
		{
			while (($file = readdir($dh)) !== false)
			{
				if (filetype($this->buildfolder . $dir . $file) == 'dir' && !in_array($file, array('.', '..')))
				{
					$this->readdir($dir . $file . '/');
				}
				elseif (filetype($this->buildfolder . $dir . $file) == 'file')
				{
					$this->md5s[$dir . $file] = md5_file($this->buildfolder . $dir . $file);
				}
			}
		}
		else
		{
			throw new Exception("Could not open path " . $dir);
		}
	}
}