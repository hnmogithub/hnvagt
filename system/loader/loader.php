<?php namespace system;
use \schedule as schedule;
use \users as users;

/**
 * Module responsible for loading modules depending on which user is logged in
 */
class loader
{
	public function __construct ()
	{
		schedule::add ( schedule::$RUN_INIT, [ $this, 'load' ] );
	}

	public function load ()
	{
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
		', [ users::current ()->id (), users::current ()->id () ] )->each ( function ( $row )
		{
			schedule::load ( $row ['path'], $row ['namespace'] );
		} );
	}
}