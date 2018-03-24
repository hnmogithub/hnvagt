<?php namespace modules;
use \schedule as schedule;
use \users as users;

class login
{
	public function __construct ()
	{
		schedule::jobAdd ( schedule::$RUN_INIT, [ $this, 'check' ] );
	}

	public function check ()
	{
		if ( users::current () == users::byId ( 1 ) )
		{
			
		}
	}
}