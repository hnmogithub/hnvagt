<?php namespace modules;
use \schedule;

class report
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], ['index'] );
	}

	public function init ( $index )
	{
		$index->add ( 'Reports', 'web/image/icon-briefcase.png', '/report/' );
	}
}