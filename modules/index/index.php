<?php namespace modules;
use \schedule as schedule;
use \template as template;

class index
{
	/** @var array $icons */
	private $icons = [];

	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], ['url'] );
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
			'icon' => template::getUrl (). $icon,
			'uri' => $uri
		];

		return $this;
	}

	/**
	 * Register the index page through the url
	 */
	public function init ( $url )
	{
		$url->request ( '^/$', (schedule::$RUN_HTML - 1), [ $this, 'run' ], ['html'] );
	}

	/**
	 * So the url registered our job in the scheduler, so lets parse the icons the other modules may have added to us
	 */
	public function run ( $html )
	{
		$icons = '';
		foreach ( $this->icons as $icon )
		{
			$icons .= $html->render ( 'web/snippets/icon.twig', $icon );
		}

		template::add ( 'web/index.twig', [ 'icons' => $icons ] );
	}
}