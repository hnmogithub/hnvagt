<?php
class template
{
	/**
	 * Stores css files to include on the page
	 * 
	 * @var array $css
	 */
	static private $css = [];

	/**
	 * Stores javascript files to include on the page
	 * 
	 * @var array $js
	 */
	static private $js = [];

	/**
	 * Stores templates added
	 * 
	 * @var array $templates
	 */
	static private $templates = [];

	/**
	 * Adds a template to the environment
	 * 
	 * @param string $path path to template
	 * @param array $environment to use in the template
	 */
	static public function add ( string $path, array $environment = [] )
	{
		self::$templates [] = [
			'path' => str_replace ( '\\', '/', schedule::lastModule () ) .'/'. $path,
			'environment' => $environment
		];
	}

	/**
	 * Adds a CSS file to be loaded
	 * 
	 * @param string $path path to css file
	 */
	static public function addCSS ( string $path )
	{
		self::$css [] = str_replace ( '\\', '/', schedule::lastModule () ) .'/'. $path;
	}

	/**
	 * Gets all templates and environments added
	 */
	static public function dump ()
	{
		return [
			'templates' => self::$templates,
			'css' => self::$css,
			'js' => self::$js
		];
	}
}