<?php
class users
{
	/**
	 * Incase of ldap error when logging in, error will be saved here
	 * 
	 * @var string $error
	 */
	static public $error = '';

	/**
	 * Authenticates a login
	 * 
	 * @param string $username
	 * @param string $password
	 * 
	 * @return user $user
	 */
	static public function login ( string $username, string $password )
	{
		self::$error = [];
		putenv('LDAPTLS_REQCERT=never');

		$settings = settings ('users');
		$conn = ldap_connect ( (string) $settings ['host'] );

		if ( $conn == false )
		{	throw new Response ('Unable to connect to ldap', 400); }

		ldap_set_option ($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option ($conn, LDAP_OPT_REFERRALS, 0);

		// Incase the LDAP Server is setup by a troll, Start-TLS does not work, GSSAPI does not work, Simple Bind does not work. We have to force this abit with a workaround, but we shall have our access.
		$login = false;
		$error =& self::$error;
		
		// -- Yes, this is an ugly hack, tell whoever to set this shit up, to do it properly so I dont have to fallback on this ugly as fuck shit
		set_error_handler ( function ( $errno, $errstr, $errfile, $errline ) use ( &$login, &$error )
		{
			if ( $errno === 2 )
			{
				list ( $function ) = explode ( ':', $errstr, 2 );
				switch ( $function )
				{
					case 'ldap_bind()':
						if ( strpos ( strtolower ( $errstr ), 'strong(er) authentication required' ) !== false )
						{
							$error [] = 'Simple Bind still does not work';

							$login = true;
							return true;
						}
						break;
					case 'ldap_start_tls()':
						$error [] = $errstr;

						return true;
					case 'ldap_sasl_bind()':
						if ( strpos ( strtolower ( $errstr ), 'invalid credentials' ) !== false )
						{	$error [] = 'GSSAPI Login failed'; }
						else
						{	$error [] = $errstr; }

						return true;
				}
			}

			return false;
		} );

		if ( (string) $settings->get('tls') !== "0" )
		{	ldap_start_tls ( $conn ); }

		if ( ldap_sasl_bind ( $conn, null, $password, 'DIGEST-MD5', null, $username ) == true )
		{	self::__loggedIn ( $username ); return true; }

		$result = @ldap_bind ( $conn, $username, $password );
		if ( ldap_get_option($conn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $err) == true )
		{	self::$error [] = $err; }

		if ( $result == true )
		{	self::__loggedIn ( $username ); return true; }
		restore_error_handler ();

		if ( $login == true )
		{	self::__loggedIn ( $username ); }

		return $login;
	}

	/**
	 * Does the actually logging in, creates the user if its not already created
	 * 
	 * @param string $username
	 */
	static private function __loggedIn ( string $username )
	{
		// These are all valid names, we need to transform these into something thats unique for both methods, so we hit the same user regardless of what the fool types in
		//
		// hnext\mo
		// mo@hnext.lan
		// 
		// testbruger1@n00bs.dk
		// hnext\testbrugern00bsdk

		if ( strpos ( $username, '\\' ) !== false )
		{	list (, $username ) = explode ( '\\', $username, 2 ); }
		$username = str_replace ( '@hnext.lan', '', $username );
		$username = str_replace ( '@', '', $username );
		$username = str_replace ( '.', '', $username );

		$result = database(DB)->cache ('users', 'id', '
			SELECT
				*

			FROM
				`users`

			WHERE
				`name` = ?

			LIMIT 1
		', [ $username ] );

		if ( $result->length () < 1 )
		{
			database(DB)->query ('
				INSERT INTO
					`users`
				(
					`name`
				)
				VALUES
				(
					?
				)
			', [ $username ] );
			$user = user::byId ( database(DB)->lastId () );

			database(DB)->query ( '
				INSERT INTO
					`groups_relations`
				(
					`user_id`,
					`group_id`
				)
				VALUES
				(
					?,
					?
				)
			', [ $user->id (), (string) settings ('users')->get ('default_group') ] );
		}
		else
		{
			$row = $result->fetchOne ();

			$user = user::byId ( $row ['id'] );
		}

		$user->set ('login', date ( 'Y-m-d H:i:s') );
		$_SESSION ['user'] = $user;
	}
}