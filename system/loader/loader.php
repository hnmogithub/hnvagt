<?php namespace system;
use \schedule as schedule;

class loader
{
	public function __construct ()
	{
		schedule::jobAdd ( schedule::$RUN_INIT, [ $this, 'load' ] );
	}

	public function load ()
	{
		database (DB)->query ('
			SELECT
				`m`.`path`,
				`m`.`namespace`
			FROM
				`modules` `m`
			
			INNER JOIN
				`modules_relations` `mr`
			ON
				`mr`.`module_id` = `m`.`id`

			WHERE
				`mr`.`user_id` = ?
		', [ users::current ()->id () ]);
	}
}