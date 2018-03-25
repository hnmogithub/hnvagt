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
		template::addCSS ('web/base.css');
		template::addJS ('web/base.js');

		template::addCSS ('//fonts.googleapis.com/css?family=Source+Sans+Pro');
		template::addJS ('//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');
	}

	public function run ()
	{
		$dump = template::dump ();

		$loader = new \Twig_Loader_Filesystem ( dirname ( $_SERVER ['SCRIPT_FILENAME'] ) .'/' );
		$twig = new \Twig_Environment ( $loader, [
			//'cache' => 'tmp/Twig/'
		] );

		$head = '';
		foreach ( $dump ['css'] as $file )
		{
			$head .= $twig->render ( 'system/html/web/snippets/css.twig', [ 'file' => $file, 'defer' => substr ( $file, 0, 2 ) == '//' ] );
		}

		foreach ( $dump ['js'] as $file )
		{
			$head .= $twig->render ( 'system/html/web/snippets/js.twig', [ 'file' => $file, 'defer' => substr ( $file, 0, 2 ) == '//' ] );
		}

		$body = '';
		foreach ( $dump ['templates'] as $template )
		{
			$body .= $twig->render ( $template ['path'], $template ['environment'] );
		}

		echo $twig->render ('system/html/web/base.twig', [
			'head' => $head,
			'body' => $body
		] );
	}
}