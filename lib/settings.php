<?php
/**
 * Container for multiple setting's
 */
class settings implements ArrayAccess
{
	/**
	 * @var string $owner
	 */
	private $owner;

	/**
	 * Constructor, sets the owner and loads the key/values there's on the class
	 *
	 * @param string $owner
	 */
	public function __construct ( string $owner )
	{
		$this->owner = $owner;

		$data =& $this->data;
		database(DB)->cache ('settings', 'id', '
			SELECT
				*

			FROM
				`settings`

			WHERE
				`owner` = ?
		', [ $owner ] )->each ( function ( $row ) use ( $data )
		{
			$setting = new setting ( $row ['id'] );

			$data [ $setting->get ('key') ] = $setting;
		} );
	}


	/**
	 * @var setting[] Contains the setting's
	 */
	private $data = [];

	/**
	 * Gets a setting based on key value
	 *
	 * @param string $key
	 *
	 * @return setting|null $object
	 */
	public function get ( string $key )
	{
		if ( isset ( $this->data [ $key ] ) == true )
		{
			return $this->data [ $key ];
		}

		return null;
	}

	/**
	 * Sets the value on setting based on key value
	 * If the key cant be found, a new key/value is created
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return true|null returns true
	 */
	public function set ( string $key, string $value )
	{
		$setting = $this->get ( $key );
		if ( $setting === null )
		{
			database(DB)->query ('
				INSERT INTO
					`settings`
				(
					`owner`,
					`key`,
					`value`
				)
				VALUES
				(
					?,
					?,
					?
				);
			', [ $this->owner, $key, $value ] );
			$id = database(DB)->lastId ();

			$setting = new setting ( $id );
			$this->data [ $key ] = $setting;

			return true;
		}
		else
		{
			$setting->set ('value', $value);

			return true;
		}
	}

	/* ArrayAccess */

	/**
	 * Checks if key exists in array
	 *
	 * @param string $key
	 *
	 * @return bool $exists
	 */
	public function offsetExists ( $key )
	{
		return isset ( $this->data [ $key ] );
	}

	/**
	 * Gets a setting from array
	 *
	 * @param string $key
	 *
	 * @return setting $setting
	 */
	public function offsetGet ( $key )
	{
		return $this->get ( $key );
	}

	/**
	 * Sets a setting to array
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return true|null true if new key were created
	 */
	public function offsetSet ( $key, $value )
	{
		return $this->set ( $key, $value );
	}

	/**
	 * Unsupported, write if needed.
	 * Deletes a setting forever (A really long time)
	 *
	 * @param string $key
	 */
	public function offsetUnset ( $key )
	{
		throw new Exception ( 'Unsupported' );
	}
}
