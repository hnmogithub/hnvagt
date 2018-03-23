<?php
class users
{
	/**
	 * Gets a user by name
	 * 
	 * @param string $name
	 * 
	 * @throws Exception if user with name not found
	 * 
	 * @return user $user
	 */
	static public function byName ( string $name )
	{
		$result = database (DB)->cache ('users', 'id', '
			SELECT
				*
			FROM
				`users`
			WHERE
				`name` = ?
		', [ $name ] );

		if ( $result->length () > 0 )
		{
			$row = $result->fetchOne ();
			return new user ( $row ['id'] );
		}
		else
		{
			throw new Exception ('users: user by name not found');
		}
	}

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
		return new user ( $id );
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

		
	}

	/**
	 * Attempts to login a user
	 * 
	 * @param string $username
	 * @param string $password
	 * 
	 * @return user $user
	 */
	static public function login ( string $username, string $password )
	{

	}
}