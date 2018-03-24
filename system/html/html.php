<?php namespace system;
use \schedule as schedule;

class html
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_HTML, [ $this, 'run' ] );
	}

	public function run ()
	{
		$loader = new \Twig_Loader_Array ([
			'index' => 'Hello {{ name }}'
		]);
		
		$twig = new \Twig_Environment ( $loader );
		echo $twig->render ('index', [ 'name' => 'testing' ] );
	}
}