<?php
class template
{
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
			'path' => str_replace ( '\\', '/', schedule::lastModule () ) .'/'.  $path,
			'environment' => $environment
		];
	}

	/**
	 * Gets all templates and environments added
	 */
	static public function get ()
	{
		return self::$templates;
	}
}