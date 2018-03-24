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
	 * Adds a template to the enviroment
	 * 
	 * @param string $path path to template
	 * @param array $enviroment to use in the template
	 */
	static public function add ( string $path, array $enviroment = [] )
	{
		self::$templates [] = [
			'path' => $path,
			'enviroment' => $enviroment
		];
	}
}