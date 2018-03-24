<?php namespace system;
use \schedule as schedule;

class loader
{
	public function __construct ()
	{
		schedule::jobAdd ( schedule::$RUN_INIT, [ $this, 'load' ] );
	}

	public function load ()
	{
		/*
		database (DB)->query ('
			SELECT
				`path`
			FROM
				`
		');
		*/
	}
}