<?php namespace modules;
use \schedule as schedule;

class report
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ] );
	}

	public function init ( $index )
	{
		
	}
}