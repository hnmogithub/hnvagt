<?php namespace modules;
use \schedule;
use \template;
use \user;

/**
 * Module for handling the creation of entries into the register
 */
class register_new
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'init' ], [ 'url' ] );
	}

	/**
	 * Bind the right url we need
	 */
	public function init ( $url )
	{
		$url->request ( '/register/new/ajax', schedule::$RUN_INIT, [ $this, 'ajax' ] );
		$url->request ( '/register/new/', schedule::$RUN_MIDDLE, [ $this, 'run' ] );
	}

	/**
	 * Ajax call, Bloodhound wanting data most likely
	 */
	public function ajax ()
	{
		$name = basename ( $_SERVER ['REQUEST_URI'] );
		switch ( $name )
		{
			case 'bSource':
				return $this->bSource ();
		}
	}

	/**
	 * Add the html stuff we need
	 */
	public function run ()
	{
		template::addCSS ( 'web/index.css' );
		template::addCSS ( 'web/lib/anytime/anytime.5.2.0.min.css' );

		template::addJS ( 'web/index.js' );


		template::add ( 'web/index.twig', [
			'user' => user::current (),
			'date_from' => date ('Y-m-d H:i')
		] );
	}

	/**
	 * Gets the sources we need, parsed in a way bloodhound understands
	 */
	private function bSource ()
	{
		$data = [];

		database (DB)->query ('
			SELECT
				`s`.*

			FROM
				`sources` `s`

			LEFT JOIN
				`reports` `r`
			ON
				`s`.`id` = `r`.`source
				AND
				`s`.`from` BETWEEN NOW() AND NOW() - INTERVAL 1 MONTH

			GROUP BY
				`s`.`id`

			ORDER BY
				COUNT(`r`.`id`) DESC, `s`.`id` ASC
		')->each ( function ( $row ) use ( &$data )
		{
			$data [ $row ['id'] ] = $row;
		} );

		return json_encode ( $data );
	}
}