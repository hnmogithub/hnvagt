<?php namespace system;
use \schedule as schedule;
use \template as template;

class html
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_HTML, [ $this, 'run' ] );

		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], ['url'] );
	}

	public function init ( $url )
	{
		$url->alias ( '/base/base.css', '/system/html/html/base.css' );
	}

	public function run ()
	{
		template::addCSS ('/base/base.css');
		$dump = template::dump ();

		$loader = new \Twig_Loader_Filesystem ( dirname ( $_SERVER ['SCRIPT_FILENAME'] ) .'/' );
		$twig = new \Twig_Environment ( $loader, [
			'cache' => 'tmp/Twig/'
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

		echo $twig->render ('system/html/html/base.twig', [ 'body' => $body ] );
	}
}