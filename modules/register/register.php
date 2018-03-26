<?php namespace modules;
use \schedule as schedule;
use \template as template;

/**
 * Module for handling registering to the register of tasks done by those who handles registers
 */
class register
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], [ 'index', 'url' ] );
	}

	/**
	 * Lets register what we need to register the places we need to register them in
	 */
	public function init ( $index, $url )
	{
		$index->add ( 'Register', 'web/image/icon-register.png', '/register/' );

		$url->request ('/register/', schedule::$RUN_MIDDLE, [ $this, 'run' ] );
	}

	/**
	 * Building the data related to what we are gonna show the user
	 */
	public function run ()
	{
		if ( isset ( $_SESSION ['register_filter'] ) == false )
		{	$_SESSION ['register_filter'] = [ user::current () ]; }
		$filter =& $_SESSION ['register_filter'];

		template::add ('web/index.twig', [
			'reports' => reports::byUsers ( $filter ),
			'filter' => $filter
		] );
	}
}