<?php namespace modules;
use \schedule as schedule;

class report_index
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], ['index'] );
	}

	public function init ( $index )
	{
		$index->add ( 'Report', 'image.png', '/report/' );
	}
}