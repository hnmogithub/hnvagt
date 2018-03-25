<?php namespace modules;
use \schedule as schedule;
use \template as template;

class index
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'check' ], ['url'] );
		schedule::paramAdd ( 'index', $this );
	}

	/**
	 * Adds an icon to the index page
	 * 
	 * @param string $text Name of the icon
	 * @param string $icon uri to the icon to use
	 * @param string $uri uri to go to on click
	 * 
	 * @return index $this
	 */
	public function add ( string $text, string $icon, string $uri )
	{
		$this->icons [] = [
			'text' => $text,
			'class' => $class,
			'uri' => $uri
		];

		return $this;
	}

	public function check ( $url )
	{
		$url->request ( '/', (schedule::$RUN_HTML - 1), [ $this, 'run' ] );
	}

	public function run ()
	{
		template::add ( 'web/index.twig' );
	}
}