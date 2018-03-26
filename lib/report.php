<?php
/**
 * Class for handling report related calls
 */
class report extends baseArray
{
	/**
	 * Gets a source by an id
	 * 
	 * @param int $id
	 * 
	 * @return reportSource $source
	 */
	static public function sourceById ( int $id )
	{
		static $sources = [];

		if ( isset ( $sources [ $id ] ) == false )
		{	$sources [ $id ] = new reportSource ( $id ); }

		return $sources [ $id ];
	}


	static private $__typesById = [];
	static private $__typesByOther = [
		false => [],
		true => []
	];

	/**
	 * Gets a type by an id
	 * 
	 * @param int $id
	 * 
	 * @return reportSource $source
	 */
	static public function typeById ( int $id )
	{
		static $type = [];

		if ( isset ( self::$__typesById [ $id ] ) == false )
		{
			$type = new reportType ( $id );

			self::$__typesById [ $id ] = $type;
			self::$__typesByOther [ $type->isOther () ][] = $type;
		}

		return $type [ $id ];
	}

	/**
	 * Gets all the types based on whenever its an other or not
	 * 
	 * @param bool $isOther default true
	 * 
	 * @return reportType[] $types
	 */
	static public function types ( bool $isOther = true )
	{
		static $loaded = false;
		if ( $loaded === false )
		{
			database(DB)->cache ('reports_types', 'id', '
				SELECT
					*

				FROM
					`reports_types`
			')->each ( function ( $row )
			{
				report::typeById ( $row ['id'] );
			} );
		}

		return self::$__typesByOther [ $isOther ];
	}

	/**
	 * 
	 */


	public function __construct ()
	{
		baseArray::__construct ( 'reports', 'id' );

		$row = cache (DB)->get ('reports', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('reports', 'id', '
				SELECT
					*

				FROM
					`reports`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('report::__construct (), id not found');
			}
		}

		$this->data = $row;
	}

	/**
	 * Gets the report id
	 * 
	 * @return int $id
	 */
	public function id ()
	{
		return $this->get ('id');
	}

	/**
	 * Gets the source of the report
	 * 
	 * @return reportSource $source
	 */
	public function source ()
	{
		return report::sourceById ( $this->get ('source') );
	}

	/**
	 * Gets the type of the report
	 * 
	 * @return reportType $type
	 */
	public function type ()
	{
		return report::typeById ( $this->get ('type') );
	}

	/**
	 * Gets the customer noted on the report
	 * 
	 * @return customer $customer
	 */
	public function customer ()
	{
		return customer::byId ( $this->get('customer') );
	}

	/**
	 * Gets the customer user assigned to the report
	 * 
	 * @return customerUser $customerUser
	 */
	public function customerUser ()
	{
		return customer::userById ( $this->get ('user') );
	}


	/** @var array $__servers */
	private $__servers = [];

	/**
	 * Gets the servers assigned to the report
	 * 
	 * @return server[] $servers
	 */
	public function servers ()
	{
		static $loaded = false;
		if ( $loaded == false )
		{
			$result = database(DB)->cache ('servers', 'id', '
				SELECT
					`s`.*

				FROM
					`servers` `s`

				INNER JOIN
					`reports_servers` `rs_1`
				ON
					`s`.`id` = `rs_1`.`server_id`

				LEFT JOIN
					`reports_servers` `rs_2`
				ON
					`rs_1`.`server_id` = `rs_2`.`server_id`

				LEFT JOIN
					`reports` `r`
				ON
					`r`.`id` = `rs_2`.`report_id`
					AND
					`r`.`customer` = ?

				WHERE
					`rs_1`.`report_id` = ?

				GROUP BY
					`s`.`id`

				ORDER BY
					COUNT(`r`.`id`) DESC
			', [ $this->get ('customer'), $this->id () ] );

			$servers =& $this->__servers;
			$result->each ( function ( $row ) use ( &$servers )
			{
				$servers [] = server::byId ( $row ['id'] );
			});
		}
	}
}