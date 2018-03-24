<?php
/**
 * Class used for handling user related calls
 */
class user extends baseArray
{
	protected $data = [];

	/**
	 * Constructor for the user class
	 * 
	 * @param int $id 
	 */
	public function __construct ( int $id )
	{
		parent::__construct ( 'users', 'id' );

		$row = cache (DB)->get ('users', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('users', 'id', '
				SELECT
					*

				FROM
					`users`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('user::__construct (), id not found');
			}
		}

		$this->data = $row;
	}

	/**
	 * Gets the user id
	 * 
	 * @return int $id
	 */
	public function id ()
	{
		return $this->get ('id');
	}
}