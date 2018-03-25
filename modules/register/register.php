<?php namespace modules;
use \schedule as schedule;

class register
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], [ 'index' ] );
	}

	public function init ( $index )
	{
		$index->add ( 'Register', 'web/image/icon-register.png', '/register/' );
	}
}