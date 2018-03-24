<?php
class template
{
	/**
	 * Stores templates added
	 * 
	 * @var array $templates
	 */
	private $templates = [];

	/**
	 * Adds a template to the environment
	 * 
	 * @param string $path path to template
	 * @param array $environment to use in the template
	 */
	static public function add ( string $path, array $environment = [] )
	{
		self::$templates [] = [
			'path' => schedule::$namespaces [ schedule::lastModule () ] .'/'. schedule::lastModule () .'/'.  $path,
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