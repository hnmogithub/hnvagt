<?php namespace modules;
use \schedule;
use \template;
use \user;

var_dump ( $_SERVER );

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
				die ( $this->bSource () );
			case 'bType':
				die ( $this->bType () );
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
	 * Gets the types we need, parsed in a way bloodhound understands
	 */
	private function bType ()
	{
		return json_encode ( database (DB)->query ('
			SELECT
				`t`.`id`,
				IFNULL(`ta`.`value`,`t`.`name`) AS `name`,
				`t`.`other`

			FROM
				`types` `t`

			LEFT JOIN
				`reports` `r`
			ON
				`r`.`type` = `t`.`id`
				AND
				`r`.`from` BETWEEN NOW() AND (NOW() - INTERVAL 1 MONTH)
				AND
				`r`.`user` = :user

			LEFT JOIN
			(
				SELECT
					`ta`.*
				FROM
					`types_aliases` `ta`

				INNER JOIN
					`types` `t`
				ON
					`t`.`id` = `ta`.`type`

				WHERE
					`ta`.`user` = :user

				GROUP BY
					`t`.`id`
			) `ta`
			ON
				`ta`.`type` = `t`.`id`

			GROUP BY
				`t`.`id`

			ORDER BY
				COUNT(`r`.`id`) DESC, `t`.`id` ASC
		', [ 'user' => user::current ()->id () ])->fetchAll () );
	}
}