<?php
class JBuilderHelperBase
{
	public function log($msg)
	{
		$cli = JCli::getInstance();
		$cli->out($msg);
	}
}