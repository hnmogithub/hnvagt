<?php
/**
 * Class used to handle server related functions
 */
class server extends baseArray
{
	/**
	 * Gets a server based on id
	 * 
	 * @param int $id
	 * 
	 * @return server $server
	 */
	static public function byId ( int $id )
	{
		static $servers = [];

		if ( isset ( $servers [ $id ] ) == false )
		{
			$servers [ $id ] = new server ( $id );
		}

		return $servers [ $id ];
	}

	/** @var array $data Used by baseArray, setting so we got access to it */
	protected $data = [];

	public function __construct ( int $id )
	{
		baseArray::__construct ( 'servers', 'id' );

		$row = cache (DB)->get ('servers', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('servers', 'id', '
				SELECT
					*

				FROM
					`servers`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('server::__construct (), id not found');
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
	 * Gets the name of the server
	 * 
	 * @return string $name
	 */
	public function __toString ()
	{
		return $this->get ('name');
	}
}