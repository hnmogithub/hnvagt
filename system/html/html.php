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
		template::addCSS ('base.css');

		$loader = new \Twig_Loader_Filesystem ( dirname ( $_SERVER ['SCRIPT_FILENAME'] ) .'/' );
		$twig = new \Twig_Environment ( $loader, [
			'cache' => 'tmp/Twig/'
		] );

		$html = '';
		foreach ( template::get () as $template )
		{
			$html .= $twig->render ( $template ['path'], $template ['environment'] );
		}

		echo $twig->render ('system/html/base/base.html', [ 'body' => $html ] );
	}
}