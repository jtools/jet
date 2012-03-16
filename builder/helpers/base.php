<?php
class JBuilderBase
{
	public function log($msg)
	{
		$cli = JCli::getInstance();
		$cli->out($msg);
	}
}