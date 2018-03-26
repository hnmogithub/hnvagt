<?php
/**
 * Handles customer type related things
 */
class customerType extends baseArray
{
	/** @var array $data Used by baseArray, setting so we got access to it */
	protected $data = [];

	public function __construct ( int $id )
	{
		baseArray::__construct ( 'customers_types', 'id' );

		$row = cache (DB)->get ('customers_types', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('customers_types', 'id', '
				SELECT
					*

				FROM
					`customers_types`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('customerType::__construct (), id not found');
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
	 * Gets the name
	 * 
	 * @return string $name
	 */
	public function __toString ()
	{
		return $this->get ('name');
	}
}