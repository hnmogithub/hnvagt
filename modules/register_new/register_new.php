<?php namespace modules;
use \schedule;
use \template;
use \user;

/**
 * Module for handling the creation of entries into the register
 */
class register_new
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], [ 'url' ] );
		echo 'works';
	}

	public function init ( $url )
	{
		$url->request ( '/register/new/', schedule::$RUN_MIDDLE, [ $this, 'run' ] );
	}

	public function run ()
	{
		template::add ( 'web/index.twig', [ 'user' => user::current () ] );
	}
}