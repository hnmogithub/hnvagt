<?php namespace system;
use \schedule as schedule;
use \template as template;

class html
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_HTML, [ $this, 'run' ] );
	}

	public function run ()
	{
		template::addCSS ('html/base.css');
		$dump = template::dump ();

		$loader = new \Twig_Loader_Filesystem ( dirname ( $_SERVER ['SCRIPT_FILENAME'] ) .'/' );
		$twig = new \Twig_Environment ( $loader, [
			'cache' => 'tmp/Twig/'
		] );

		$html = '';
		foreach ( $dump ['templates'] as $template )
		{
			$html .= $twig->render ( $template ['path'], $template ['environment'] );
		}

		echo $twig->render ('system/html/html/base.html', [ 'body' => $html ] );
	}
}