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
		$url->request ( '/ajax/login', schedule::$RUN_INIT, [ $this, 'ajax' ] );
		//$url->alias ( '/login/login.css', '/modules/login/login.css' );
	}

	public function ajax ()
	{
		var_dump ( $_POST );
		die ();
	}

	public function run ()
	{
		template::addCSS ('login.css');
		template::add ('login.twig', [
			'js' => template::getUrl (). '/login.js'
		] );
	}
}