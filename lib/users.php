<?php
class users
{
	static private $users = [];

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

			if ( isset ( self::$users [ $row ['id'] ] ) == false )
			{	self::$users [ $row ['id'] ] = new user ( $row ['id'] ); }

			return self::$users [ $row ['id'] ];

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
		if ( isset ( self::$users [ $id ] ) == false )
		{	self::$users [ $id ] = new user ( $id ); }

		return self::$users [ $id ];
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

		if ( isset ( $_SESSION ['user_id'] ) == false )
		{	$_SESSION ['user_id'] = 1; }

		if ( isset ( self::$users [ $_SESSION ['user_id'] ] ) == false )
		{	self::$users [ $_SESSION ['user_id'] ] = new user ( $_SESSION ['user_id'] ); }

		return self::$users [ $_SESSION ['user_id'] ];
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
		putenv('LDAPTLS_REQCERT=never');

		$settings = settings ('users');
		$conn = ldap_connect ( (string) $settings ['host'] );

		ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

		if ( $conn == false )
		{	throw new Response ('Unable to connect to ldap', 400); }

		if ( ldap_start_tls ( $conn ) == false )
		{	throw new Response ('Unable to start TLS on ldap', 400); }

		if ( ldap_bind ( $conn, $username, $password ) == false )
		{	throw new Response ('Unable to bind ldap', 400); }

		echo 'success?';
	}
}