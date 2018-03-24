<?php namespace system;
use \schedule as schedule;
use \users as users;
use \Response as Response;

/**
 * Module responsible for loading modules depending on which user is logged in
 */
class loader
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'file' ] );
		schedule::add ( schedule::$RUN_INIT, [ $this, 'load' ] );

		schedule::paramAdd ( 'loader', $this );
	}

	/**
	 * Contains uri aliases
	 * 
	 * @var array $aliases
	 */
	private $aliases = [];

	/**
	 * Generation of aliases for raw files
	 * 
	 * @param string $new 
	 * @param string $real 
	 */
	public function uriAlias ( string $new, string $real )
	{
		$this->aliases [ $new ] = $real;
	}



	/**
	 * Lets check if we are asking for a file, if we are, return that file
	 */
	public function file ()
	{
		$url = $_SERVER ['REQUEST_URI'];
		if ( isset ( $this->aliases [ $url ] ) == true )
		{	$url = $this->aliases [ $url ]; }

		$file = explode ( '.', basename ( $url ) );
		$extension = strtolower ( array_pop ( $file ) );

		switch ( $extension )
		{
			case 'css':
			case 'js':
				if ( file_exists ( '.'. $url ) == false )
				{	throw new Response ( 'File not found ('. $_SERVER ['REQUEST_URI'] .')', 404 ); }
				$type = [
					'css' => 'text/css;charset=utf-8',
					'js' => 'text/javascript;charset=utf-8'
				];
				header ( 'Content-Type:'. $type [ $extension ] );
				
				die ( file_get_contents ( '.'. $url ) );
		}
	}

	public function load ()
	{
		$id = users::current ()->id ();

		database (DB)->query ('
			SELECT
				`m`.`path`,
				`m`.`namespace`,
				IFNULL(`mr_u`.`type`,`mr_g`.`type`) AS `type`

			FROM
				`modules` `m`
			
			LEFT JOIN
				`modules_relations` `mr_u`
			ON
				`mr_u`.`module_id` = `m`.`id`
				AND
				`mr_u`.`type` = "user"

			LEFT JOIN
			(
				SELECT
					*
				FROM
					`modules_relations` `mr`

				INNER JOIN
					`group_relations` `gr`
				ON
					`gr`.`group_id` = `mr`.`id`
					AND
					`mr`.`type` = "group"
			) `mr_g`
			ON
				`mr_g`.`module_id` = `m`.`id`
				
			WHERE
				`mr_u`.`id` = ?
				OR
				`mr_g`.`user_id` = ?
		', [ $id, $id ] )->each ( function ( $row )
		{
			schedule::load ( $row ['path'], $row ['namespace'] );
		} );
	}
}