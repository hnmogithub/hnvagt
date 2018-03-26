<?php
/**
 * Class used to handle customer related things
 */
class customer extends baseArray
{
	/**
	 * Gets a customer by id
	 * 
	 * @param int $id
	 * 
	 * @return customer $customer
	 */
	static public function byId ( int $id )
	{
		static $customers = [];

		if ( isset ( $customers [ $id ] ) == false )
		{
			$customers [ $id ] = new customer ( $id );
		}

		return $customers [ $id ];
	}

	/**
	 * Gets the customer type by id
	 * 
	 * @param int $id
	 * 
	 * @return customerType $type
	 */
	static public function typeById ( int $id )
	{
		static $types = [];

		if ( isset ( $types [ $id ] ) == false )
		{
			$types [ $id ] = new customerType ( $id );
		}

		return $types [ $id ];
	}

	/**
	 * Gets a customer user by id
	 * 
	 * @param int $id
	 * 
	 * @return customerUser $customerUser
	 */
	static public function userById ( int $id )
	{
		static $users = [];

		if ( isset ( $users [ $id ] ) == false )
		{
			$users [ $id ] = new customerUser ( $id );
		}

		return $users [ $id ];
	}


	public function __construct ( int $id )
	{
		baseArray::__construct ( 'customers', 'id' );

		$row = cache (DB)->get ('customers', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('customers', 'id', '
				SELECT
					*

				FROM
					`customers`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('customer::__construct (), id not found');
			}
		}

		$this->data = $row;
	}

	/**
	 * Gets the id
	 * 
	 * @return int $id
	 */
	public function id ()
	{
		return $this->get ('id');
	}

	/**
	 * Gets the customer type
	 * 
	 * @return customerType $type
	 */
	public function type ()
	{
		return customer::typeById ( $this->get ('type') );
	}

	/** @var array $__users */
	private $__users = [];

	/**
	 * Gets the users attached to this customer
	 * 
	 * @return customerUser[] $users
	 */
	public function users ()
	{
		if ( isset ( $users [0] ) == false )
		{
			$result = database(DB)->cache ('customers_users', 'id', '
				SELECT
					`cu`.*

				FROM
					`customers_users` `cu`

				WHERE
					`customer_id` = ?

				LEFT JOIN
					`reports` `r`
				ON
					`r`.`user` = `cu`.`id`
					AND
					`r`.`from` > NOW() - INTERVAL 1 MONTH

				GROUP BY
					`cu`.`id`

				ORDER BY
					COUNT(`r`.`id`) DESC
			', [ $this->id () ] );

			$users =& $this->__users;
			$result->each ( function ( $row ) use ( &$users )
			{
				$users [] = customer::userById ( $row ['id'] );
			} );
		}

		return $this->__users;
	}

	/**
	 * Gets the name of the customer
	 * 
	 * @return string $name
	 */
	public function __toString ()
	{
		return $this->get ('name');
	}
}