<?php
/**
 * Handles report source related things
 */
class reportSource extends baseArray
{
	/** @var array $data Used by baseArray, setting so we got access to it */
	protected $data = [];

	public function __construct ( int $id )
	{
		baseArray::__construct ('sources', 'id');

		$row = cache (DB)->get ('sources', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('sources', 'id', '
				SELECT
					*

				FROM
					`sources`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('reportSource::__construct (), id not found');
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