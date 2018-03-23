<?php
/**
 * Base object for most objects used, objects centers around a database row, this handles the getting/setting of it
 */
abstract class base
{
	/** @var array used by base specified here so current class have access to it */
	protected $data;

	/** @var string Contains which table we are operating on */
	private $__table;

	/** @var string tells which column is the unique identifier */
	private $__id;

	/**
	 * Constructor function, tells which database table and which column is the unique it can update out from
	 * the id provided is used when updating the row
	 *
	 * @param string $table database table name
	 * @param string $id id used to update the row, should be an unique unless intending to make multiple data exactly the same
	 */
	protected function __construct ( $table, $id )
	{
		$this->__table = $table;
		$this->__id = $id;
	}

	/**
	 * Function used to check if a column exists in the row we represent, using this as isset ( this->data [ key ] ) will return false if the key is null
	 *
	 * @param string $key Optional default null
	 * @return bool
	 */
	protected function hasKey ( $key = null )
	{
		static $keys = [];
		if ( isset ( $keys [ $this->__table ] ) == false )
		{
			if ( is_array ( $this->data ) == false )
			{	return false; }

			foreach ( $this->data as $key => $value )
			{	$keys [ $this->__table ][$key] = true; }
		}

		return isset ( $keys [ $this->__table ][ $key ] );
	}

	/**
	 * Gets the key out of the row we are representing
	 *
	 * @param string $key
	 *
	 * @throws Exception if __construct hasn't run
	 * @throws InvalidArgumentException if trying to access a column that doesn't exist
	 *
	 * @return mixed
	 */
	public function get ( $key )
	{
		if ( $this->__table == null )
		{	throw new Exception ( get_class($this) .': base needs to be initialized properly'); }

		if ( $this->hasKey ( $key ) == false && $this->__id !== $key )
		{	throw new InvalidArgumentException ( get_class($this) .': Requesting illegal offset: ' . $key ); }

		if ( isset ( $this->data [$key] ) == false )
		{	return null; }

		return $this->data [ $key ];
	}

	/**
	 * Updates database row containing the store, changes $key, $value into $key = [ $key => $value ];
	 * $this->set ([ value1 => 'a', value2 => 'b' ]) works
	 *
	 * @param mixed $key
	 * @param mixed $value Optional, default null
	 *
	 * @throws Exception if __construct hasn't run
	 * @throws InvalidArgumentException if trying to set column that doesn't exist or trying to set a value thats not string or numeric
	 */
	public function set ( $key, $value = null )
	{
		if ( $this->__table == null )
		{	throw new Exception ( get_class($this) .': base needs to be initialized properly'); }

		if ( $value !== null && is_array ( $key ) == false )
		{
			$key = [ $key => $value ];
		}

		if ( is_array ( $key ) == false )
		{	throw new InvalidArgumentException ( get_class($this) .': Trying to use illegal offset or setting offset to null' ); }

		reset ( $key );
		while ( list ( $i, $value ) = each ( $key ) )
		{
			if ( $this->hasKey ( $i ) == false || $i == $this->__id )
			{	throw new InvalidArgumentException ( get_class($this) .': Trying to set illegal offset: ' . $i ); }

			if ( is_string ( $value ) == false && is_numeric ( $value ) == false && is_null ( $value ) == false )
			{	throw new InvalidArgumentException ( get_class($this) .': Trying to set none string/numeric value to offset: ' . $i . ', type: ' . gettype ( $value ) ); }
		}

		$data = array_merge ( $this->data, $key );
		if ( isset ( $data [ $this->__id ] ) == false )
		{
			database (DB)->query ('
				INSERT INTO
					`'. $this->__table .'`
				(
					'. join ( ',', array_keys ( $data ) ) .'
				)
				VALUES
				(
					'. substr ( str_repeat ('?,', count ( $data ) ) ,0, -1 ) .'
				)
			', array_values ( $data ) );
			$data [ $this->__id ] = database(DB)->lastId ();
		}
		else
		{
			database (DB)->query ('
				UPDATE
					`'. $this->__table .'`
				SET
					`'. join ('` = ?,', array_keys ( $key ) ) .'` = ?
				WHERE
					`'. $this->__id .'` = ?
			', array_values ( array_merge ( $key, [ $this->data [ $this->__id ] ] ) ) );
		}

		$this->data = $data;
		return $this;
	}

	private function __setBuild ( $table )
	{
		$string = [];
		foreach ( $table as $key => $value )
		{
			$string [] = '`'. $key .'` = ?';
		}

		return join  (',', $string );
	}
}
