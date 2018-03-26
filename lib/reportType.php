<?php
/**
 * Handles report type related things
 */
class reportType extends baseArray
{
	/** @var array $data Used by baseArray, setting so we got access to it */
	protected $data = [];

	public function __construct ( int $id )
	{
		baseArray::__construct ('reports_types', 'id');

		$row = cache (DB)->get ('reports_types', $id );
		if ( $row == null )
		{
			$row = database(DB)->cache ('reports_types', 'id', '
				SELECT
					*

				FROM
					`reports_types`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('reportType::__construct (), id not found');
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
		return $this->id ();
	}

	/**
	 * Checks if this is an other type or not
	 * 
	 * @return bool $state
	 */
	public function isOther ()
	{
		return $this->get ('other') == 1;
	}

	/**
	 * Gets the name of the type
	 * 
	 * @return string $name
	 */
	public function __toString ()
	{
		return $this->get ('name');
	}
}