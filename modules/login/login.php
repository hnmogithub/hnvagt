<?php namespace modules;
use \schedule as schedule;
use \users as users;

class login
{
	public function __construct ()
	{
		schedule::jobAdd ( 1, [ $this, 'run' ] );
	}

	public function run ()
	{
		
	}
}