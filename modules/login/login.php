<?php namespace modules;
use \schedule as schedule;

class login
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'check' ] );
	}

	public function check ()
	{
		template::add ('login.twig', [] );
	}
}