<?php
/**
 * Class for handling a customer user
 */
class customerUser extends baseArray
{
	protected $data = [];

	public function __construct ( int $id )
	{
		baseArray::__construct ( 'customers_users', 'id' );

		$row = cache (DB)->get ('customers_users', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('customers_users', 'id', '
				SELECT
					*

				FROM
					`customers_users`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('customerUser::__construct (), id not found');
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
	 * Gets the name of the user
	 * 
	 * @return string $name
	 */
	public function __toString ()
	{
		return $this->get ('name');
	}
}