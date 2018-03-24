<?php namespace modules;
use \schedule as schedule;
use \template as template;

class login
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], ['url'] );
		schedule::add ( schedule::$RUN_MIDDLE, [ $this, 'run' ] );
	}

	public function init ( $url )
	{
		$url->alias ( '/login/login.css', '/system/login/login.css' );
	}

	public function check ()
	{
		template::addCSS ('/login/login.css');
		template::add ('login.twig', [] );
	}
}