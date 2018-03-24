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
			'path' => self::getUrl () .'/'. $path,
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
		if ( substr ( $path, 0, 4 ) == 'http' )
		{	throw new Exception ('template::addCSS (), path added starts with http, do not add paths like this, use //domain.ads/asd/asd.css instead' ); }

		if ( substr ( $path, 0, 1 ) != '/' )
		{
			$path = self::getUrl () .'/'. $path;
		}

		self::$css [] = $path;
	}

	/**
	 * Adds a javascript file to be loaded
	 * 
	 * @param string $path path to javascript file
	 */
	static public function addJS ( string $path )
	{
		if ( substr ( $path, 0, 4 ) == 'http' )
		{	throw new Exception ('template::addJS (), path added starts with http, do not add paths like this, use //domain.ads/asd/asd.js instead' ); }

		if ( substr ( $path, 0, 1 ) != '/' )
		{
			$path = self::getUrl () .'/'. $path;
		}

		self::$js [] = $path;
	}

	/**
	 * Gets the url needed to target the current module
	 * 
	 * @return string $path
	 */
	static public function getUrl ()
	{
		return str_replace ( '\\', '/', schedule::lastModule () );
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