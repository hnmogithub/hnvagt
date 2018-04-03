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
			case 'nCustomer':
				die ( $this->nCustomer () );
			case 'bCustomer':
				die ( $this->bCustomer () );
			
			case 'customerTypes':
				die ( $this->customerTypes () );

			case 'nCustomerUser':
				die ( $this->nCustomerUser () );
			case 'bCustomerUser':
				die ( $this->bCustomerUser () );
			
			case 'bLocation':
				die ( $this->bLocation () );
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
	 * Creates a new customer
	 */
	private function nCustomer ()
	{
		if ( isset ( $_POST ['type'] ) == false || isset ( $_POST ['name'] ) == false )
		{	die ( '{"error": "Missing data"}' ); }

		try
		{
			database(DB)->Query ('
				INSERT INTO
					`customers`
				(
					`name`,
					`type`
				)
				VALUES
				(
					?,
					?
				)
			', [ $_POST ['name'], $_POST ['type'] ] );
		}
		catch ( Exception $e )
		{
			if ( $e->getCode () == 23000 )
			{	return json_encode ( ['error' => 'Customer with that name already exists'] ); }
			return json_encode ( ['error' => $e->getMessage () ] );
		}

		return json_encode ( [
			'id' => database(DB)->lastId (),
			'name' => $_POST ['name'],
			'type' => $_POST ['type']
		] );
	}

	/**
	 * Get customers, parsed in a way bloodhound understands
	 */
	private function bCustomer ()
	{
		if ( isset ( $_GET ['prefetch'] ) == true && $_GET ['prefetch'] === 'true' )
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

				GROUP BY
					`c`.`id`
				
				ORDER BY
					COUNT(`r`.`id`) DESC

				LIMIT 10
			' )->fetchAll () );
		}
		else
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
				
				LIMIT 10
			', [ $_POST ['source'], $_GET ['search'] ] )->fetchAll () );
		}
	}

	/**
	 * Gets customer types, returns in json
	 */
	private function customerTypes ()
	{
		return json_encode ( database(DB)->query ('
			SELECT
				*
			
			FROM
				`customers_types`

			ORDER BY
				`id` ASC
		')->fetchAll () );
	}

	/**
	 * Creates a new customer user
	 */
	private function nCustomerUser ()
	{
		if ( isset ( $_POST ['customer'] ) == false || isset ( $_POST ['name'] ) == false )
		{	die ( '{"error": "Missing data"}' ); }

		try
		{
			database(DB)->Query ('
				INSERT INTO
					`customers_users`
				(
					`name`,
					`customer`
				)
				VALUES
				(
					?,
					?
				)
			', [ $_POST ['name'], $_POST ['customer'] ] );
		}
		catch ( Exception $e )
		{
			if ( $e->getCode () == 23000 )
			{	return json_encode ( ['error' => 'Customer user with that name already exists'] ); }
			return json_encode ( ['error' => $e->getMessage () ] );
		}

		return json_encode ( [
			'id' => database(DB)->lastId (),
			'name' => $_POST ['name'],
			'customer' => $_POST ['customer']
		] );
	}

	/**
	 * Gets relevant users, parsed in a way bloodhound understands
	 */
	private function bCustomerUser ()
	{
		if ( isset ( $_GET ['prefetch'] ) == true && $_GET ['prefetch'] == 'true' )
		{
			$order = '';
			if ( isset ( $_POST ['customer'] ) == true && is_numeric ( $_POST ['customer'] ) === true )
			{
				$order = 'CASE WHEN `cu`.`customer` = '. database(DB)->quote ( (int)$_POST ['customer'] ) .' THEN 1 ELSE 0 END DESC,';
			}

			$query = '
				SELECT
					`cu`.*

				FROM
					`customers_users` `cu`

				LEFT JOIN
					`reports` `r`
				ON
					`r`.`customerUser` = `cu`.`id`

				GROUP BY
					`cu`.`id`

				ORDER BY
					'. $order .' COUNT(`r`.`id`) DESC

				LIMIT 10
			';
			var_dump ( $query );

			return json_encode ( database (DB)->query ($query)->fetchAll () );
		}
		else
		{
			if ( isset ( $_POST ['customer'] ) == false || isset ( $_POST ['type'] ) == false || isset ( $_GET ['search'] ) == false )
			{	throw new Response ('Missing arguments', 421 ); }
			/**
			 * This query might need a rewrite if this db grows massive
			 */

			return json_encode ( database (DB)->query ('
				SELECT
					`cu`.*
				
				FROM
					`customers_users` `cu`
				
				LEFT JOIN
					`customers` `c`
				ON
					`cu`.`customer` = `c`.`id`
					AND
					`c`.`id` = ?

				LEFT JOIN
					`reports` `r`
				ON
					`r`.`customerUser` = `cu`.`id`
					AND
					`r`.`type` = ?

				WHERE
					`cu`.`name` LIKE CONCAT("%",?,"%")

				GROUP BY
					`cu`.`id`

				ORDER BY
					CASE
						WHEN `c`.`id` IS NOT NULL THEN 1
						WHEN `c`.`id` IS NULL THEN 0
					END DESC, COUNT(`r`.`id`) DESC, `cu`.`id` ASC

				LIMIT 10
			', [ $_POST ['customer'],  $_POST ['type'], $_GET ['search'] ])->fetchAll () );
		}
	}

	/**
	 * Get locations, parsed in a way bloodhound understands
	 */
	private function bLocation ()
	{
		if ( isset ( $_GET ['prefetch'] ) == true && $_GET ['prefetch'] == 'true' )
		{
			return json_encode ( database (DB)->query ('
				SELECT
					1 AS `id`,
					`r`.`location` AS `name`

				FROM
					`reports` `r`
				
				LEFT JOIN
					`reports` `r1`
				ON
					`r1`.`location` = `r`.`location`
					AND
					`r1`.`from` BETWEEN NOW() AND (NOW() - INTERVAL 1 MONTH)
				
				WHERE
					`r`.`location` IS NOT NULL

				GROUP BY
					`r`.`id`

				ORDER BY
					COUNT(`r`.`id`) DESC

				LIMIT 10
			')->fetchAll () );
		}
		else
		{
			return json_encode ( database (DB)->query ('
				SELECT
					1 AS `id`,
					`r`.`location` AS `name`

				FROM
					`reports` `r`

				LEFT JOIN
					`reports` `r1`
				ON
					`r1`.`location` = `r`.`location`
					AND
					`r1`.`from` BETWEEN NOW() AND (NOW() - INTERVAL 1 MONTH)
					AND
					`r1`.`customerUser` = ?

				WHERE
					`r`.`location` IS NOT NULL

				LIMIT 10
			')->fetchAll () );
		}
	}
}