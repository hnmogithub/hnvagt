<?php namespace modules;
use \schedule as schedule;
use \template as template;

class login
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'check' ] );
	}

	public function check ()
	{
		echo 'ran';
		template::add ('login.twig', [] );
	}
}