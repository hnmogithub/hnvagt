<?php
class reportSource extends baseArray
{
	public function __construct ( int $id )
	{
		baseArray::__construct ('reports_sources', 'id');

		$row = cache (DB)->get ('reports_sources', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('reports_sources', 'id', '
				SELECT
					*

				FROM
					`reports_sources`

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