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
		list ( $name ) = explode( '?', $name , 2 );
		switch ( $name )
		{
			case 'bSource':
				die ( $this->bSource () );
			case 'nType':
				die ( $this->nType () );
			case 'bType':
				die ( $this->bType () );
			case 'bCustomer':
				die ( $this->bCustomer () );
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
		return json_encode ( database (DB)->query ('
			SELECT
				`s`.*

			FROM
				`sources` `s`

			LEFT JOIN
				`reports` `r`
			ON
				`s`.`id` = `r`.`source`
				AND
				`r`.`from` BETWEEN NOW() AND (NOW() - INTERVAL 1 MONTH)

			GROUP BY
				`s`.`id`

			ORDER BY
				COUNT(`r`.`id`) DESC, `s`.`id` ASC
		')->fetchAll () );
	}
	
	/**
	 * We got request to make a new type
	 */
	private function nType ()
	{
		database (DB)->query ('
			INSERT INTO
				`types`
			(
				`name`,
				`other`,
				`created_by`
			)
			VALUES
			(
				?,
				?,
				?
			)
		', [ $_POST ['name'], 1, user::current ()->id () ]);

		return json_encode ( [
			'id' => database (DB)->lastId (), 
			'name' => $_POST ['name'], 
			'created_by' => user::current ()->get ('name'), 
			'created_at' => date ('Y-m-d H:i')
		] );
	}

	/**
	 * Gets the types we need, parsed in a way bloodhound understands
	 */
	private function bType ()
	{
		$id = user::current ()->id ();

		return json_encode ( database (DB)->query ('
			SELECT
				`t`.`id`,
				IFNULL(`ta`.`value`,`t`.`name`) AS `name`,
				`t`.`other`,
				IFNULL(`ta`.`username`, `u`.`name`) AS `created_by`,
				IFNULL(`ta`.`created_at`,`t`.`created_at`) AS `created_at`

			FROM
				`types` `t`

			INNER JOIN
				`users` `u`
			ON
				`t`.`created_by` = `u`.`id`

			LEFT JOIN
				`reports` `r`
			ON
				`r`.`type` = `t`.`id`
				AND
				`r`.`from` BETWEEN NOW() AND (NOW() - INTERVAL 1 MONTH)
				AND
				`r`.`user` = ?

			LEFT JOIN
			(
				SELECT
					`ta`.*,
					`u`.`name` as `username`

				FROM
					`types_aliases` `ta`

				INNER JOIN
					`types` `t`
				ON
					`t`.`id` = `ta`.`type`

				INNER JOIN
					`users` `u`
				ON
					`u`.`id` = `ta`.`user`

				WHERE
					`ta`.`user` = ?

				GROUP BY
					`t`.`id`
			) `ta`
			ON
				`ta`.`type` = `t`.`id`

			GROUP BY
				`t`.`id`

			ORDER BY
				COUNT(`r`.`id`) DESC, `t`.`id` ASC
		', [ $id, $id ])->fetchAll () );
	}

	/**
	 * Get customers, parsed in a way bloodhound understands
	 */
	public function bCustomer ()
	{
		return json_encode ( database(DB)->query ('
			SELECT
				`c`.*

			FROM
				`customers` `c`

			LEFT JOIN
				`reports` `r`
			ON
				`r`.`customer` = `c`.`id`
				AND
				`r`.`from` BETWEEN NOW() AND (NOW() - INTERVAL 1 MONTH)

			LEFT JOIN
				`reports` `r1`
			ON
				`r1`.`customer` = `c`.`id`
				AND
				`r`.`from` BETWEEN NOW() AND (NOW() - INTERVAL 1 MONTH)
				AND
				`r`.`type` = ?

			WHERE
				`c`.`name` LIKE CONCAT("%",?, "%")

			GROUP BY
				`c`.`id`
			
			ORDER BY
				GREATEST(COUNT(`r1`.`id`) + GREATEST(10, COUNT(`r`.`id`) * 0.1), COUNT(`r`.`id`)) DESC
		', [ $_POST ['source'], $_GET ['search'] ] )->fetchAll () );
	}
}