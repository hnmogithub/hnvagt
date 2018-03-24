<?php
/**
 * Repressents a setting row
 */
class setting extends Base_Array
{
	/**
	 *
	 */
	public function __construct ( int $id )
	{
		Base_Array::__construct ('settings', 'id');

		// --
		$row = cache(DB)->get ('settings', $id);
		if ( $row == null )
		{
			$row = database(DB)->cache ('settings', 'id', '
				SELECT
					*

				FROM
					`settings`

				WHERE
					`id` = ?

				LIMIT 1
			', [ $id ] )->fetchOne ();

			if ( $row == null )
			{
				throw new Exception ('setting::__construct (), id not found');
			}
		}

		$this->data = $row;
	}

	/**
	 * Gets the setting id
	 *
	 * @return int $id
	 */
	public function id ()
	{
		return $this->get ('id');
	}

	/**
	 * Converts object to string
	 *
	 * @return string $value
	 */
	public function __toString ()
	{
		return $this->get ('value');
	}
}
