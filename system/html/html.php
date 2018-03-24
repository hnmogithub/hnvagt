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
		$loader = new \Twig_Loader_Filesystem ('.');
		$twig = new \Twig_Environment ( $loader );

		var_dump ( template::get () );

		$html = '';
		foreach ( template::get () as $template )
		{
			echo 'loading: '. $template ['path'];

			$html .= $twig->render ( $template ['path'], $template ['environment'] );
		}

		echo $html;
	}
}