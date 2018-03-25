<?php namespace modules;
use \schedule as schedule;

class logout
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], ['url'] );
		schedule::add ( (schedule::$RUN_HTML - 2), [ $this, 'run' ], ['index'] );
	}

	public function init ( $url )
	{
		$url->request ('/logout', schedule::$RUN_INIT, [ $this, 'logout' ] );
	}

	public function logout ()
	{
		unset ( $_SESSION ['user'] );

		header ('Location: /' );
		die ();
	}

	public function run ( $index )
	{
		$index->add ( 'Logout', 'web/image/icon-door.png', '/logout' );
	}
}