<?php namespace system;
use \schedule as schedule;
use \template as template;

/**
 * Handles the html part of the site
 */
class html
{
	/** @var Twig_Loader_Filesystem $loader */
	private $loader = null;

	/** @var Twig_Environment $twig */
	private $twig = null;

	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], ['url'] );
		schedule::add ( schedule::$RUN_HTML, [ $this, 'run' ] );
		schedule::paramAdd ( 'html', $this );

		$this->loader = new \Twig_Loader_Filesystem ( dirname ( $_SERVER ['SCRIPT_FILENAME'] ) .'/' );
		$this->twig = new \Twig_Environment ( $this->loader, [
			//'cache' => 'tmp/Twig/'
		] );
	}

	/**
	 * Renders a template using environment provided
	 * 
	 * @param string $template path to template
	 * @param array $environment 
	 * 
	 * @return string $html
	 */
	public function render ( string $template, array $environment = [] )
	{
		$template = template::getUrl () . $template;

		return $this->twig->render ( $template, $environment );
	}

	// --

	/**
	 * Adds the the layout to the template
	 */
	public function init ( $url )
	{
		template::addCSS ('web/base.css');
		template::addJS ('web/base.js');

		template::addCSS ('//fonts.googleapis.com/css?family=Source+Sans+Pro');
		template::addJS ('//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js');
	}

	/**
	 * Generates the html based on what have been added to the template class
	 */
	public function run ()
	{
		$dump = template::dump ();

		$head = '';
		foreach ( $dump ['css'] as $file )
		{
			$head .= $this->twig->render ( 'system/html/web/snippets/css.twig', [ 'file' => $file, 'defer' => substr ( $file, 0, 2 ) == '//' ] );
		}

		foreach ( $dump ['js'] as $file )
		{
			$head .= $this->twig->render ( 'system/html/web/snippets/js.twig', [ 'file' => $file, 'defer' => substr ( $file, 0, 2 ) == '//' ] );
		}

		$body = '';
		foreach ( $dump ['templates'] as $template )
		{
			echo $template ['path'];
			$body .= $this->twig->render ( $template ['path'], $template ['environment'] );
		}

		// We do not use die incase some modules need to run after the layout have been generated
		echo $this->twig->render ('system/html/web/base.twig', [
			'head' => $head,
			'body' => $body
		] );
	}
}