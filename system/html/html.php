<?php namespace system;
use \schedule as schedule;
use \template as template;

class html
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], ['url'] );
		schedule::add ( schedule::$RUN_HTML, [ $this, 'run' ] );
	}

	public function init ( $url )
	{
		
	}

	public function run ()
	{
		template::addCSS ('html/base.css');
		template::addCSS ('//fonts.googleapis.com/css?family=Source+Sans+Pro');
		$dump = template::dump ();

		$loader = new \Twig_Loader_Filesystem ( dirname ( $_SERVER ['SCRIPT_FILENAME'] ) .'/' );
		$twig = new \Twig_Environment ( $loader, [
			//'cache' => 'tmp/Twig/'
		] );

		$head = '';
		foreach ( $dump ['css'] as $file )
		{
			$head .= $twig->render ( 'system/html/html/snippets/css.twig', [ 'file' => $file ] );
		}

		$body = '';
		foreach ( $dump ['templates'] as $template )
		{
			$body .= $twig->render ( $template ['path'], $template ['environment'] );
		}

		echo $twig->render ('system/html/html/base.twig', [
			'head' => $head,
			'body' => $body
		] );
	}
}