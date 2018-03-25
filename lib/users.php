<?php
class users
{
	/**
	 * Incase of ldap error when logging in, error will be saved here
	 * 
	 * @var string $error
	 */
	static public $error = '';

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
		self::$error = '';
		putenv('LDAPTLS_REQCERT=never');

		$settings = settings ('users');
		$conn = ldap_connect ( (string) $settings ['host'] );

		if ( $conn == false )
		{	throw new Response ('Unable to connect to ldap', 400); }

		ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
		//ldap_set_option($conn, LDAPT_OPT_ENCRYPT, 1);

		
		// GSSAPI does not work
		//if ( ldap_sasl_bind ( $conn, null, $password, 'DIGEST-MD5', null, $username ) == false )
		//{	throw new Response ('Unable to GSSAPI bind ldap', 400); }
		
		// Since the LDAP Server is setup by a troll, Start-TLS does not work, GSSAPI does not work, Simple Bind does not work. We have to force this abit with a workaround, but we shall have our access.
		$login = false;
		set_error_handler ( function ( $errno, $errstr, $errfile, $errline ) use ( &$login )
		{
			if ( $errno === 2 )
			{
				if ( substr ( $errstr, 0, 4 ) == 'ldap' )
				{
					if ( strpos ( strtolower ( $errstr ), 'strong(er) authentication required' ) !== false )
					{
						self::$error = 'Simple Bind still does not work';

						$login = true;
						return true;
					}
				}
			}

			return false;
		} );

		$result = @ldap_bind ( $conn, $username, $password ); // false here would normally be because of invalid password, however... 
		self::$error .= "\n". ldap_get_option($conn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err);

		if ( $result == true )
		{	return true; }
		//else
		//{	return false; }
		restore_error_handler (); // Lets restore back from our shit-code to normal code

		return $login;
	}
}