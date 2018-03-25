<?php namespace modules;
use \schedule as schedule;
use \template as template;
use \users as users;
use \Response as Response;


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
		if ( isset ( $_POST ['username'] ) == false || isset ( $_POST ['password'] ) == false )
		{	throw new Response ('Missing argument', 422); }

		$username = $_POST ['username'];
		$password = $_POST ['password'];

		if ( users::login ( $username, $password ) === true )
		{
			die (json_encode ([
				'state' => 1,
				'error' => users::$error
			]));
		}
		else
		{
			die (json_encode ([
				'state' => 0,
				'error' => users::$error
			]));
		}
	}

	public function run ()
	{
		template::addCSS ('login.css');
		template::add ('login.twig', [
			'js' => template::getUrl (). '/login.js'
		] );
	}
}