<?php namespace modules;
use \schedule as schedule;
use \template as template;

class index
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_MIDDLE, [ $this, 'run' ] );
	}

	public function run ()
	{
		template::add ('web/index.twig');
	}
}