<?php
/**
 * Class used for handling user related calls
 */
class user extends baseArray
{
	/**
	 * Gets a user by id
	 * 
	 * @param int $id
	 * 
	 * @throws Exception if user with id not found
	 * 
	 * @return user $user
	 */
	static public function byId ( int $id )
	{
		static $users = [];

		if ( isset ( $users [ $id ] ) == false )
		{
			$users [ $id ] = new user ( $id );
		}

		return $users [ $id ];
	}

	/**
	 * Gets the current logged in user
	 * 
	 * @return user $user
	 */
	static public function current ()
	{
		if ( session_status () == PHP_SESSION_NONE )
		{   session_start (); }

		if ( isset ( $_SESSION ['user'] ) == false )
		{	$_SESSION ['user'] = user::byId ( 1 ); }

		return $_SESSION ['user'];
	}

	/** @var array $data Used by baseArray, setting so we got access to it */
	protected $data = [];

	/**
	 * Constructor for the user class
	 * 
	 * @param int $id 
	 */
	public function __construct ( int $id )
	{
		baseArray::__construct ( 'users', 'id' );

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