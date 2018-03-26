<?php
class reports
{
	/**
	 * Gets all reports by a user (user, not customerUser)
	 * 
	 * @param user[] $user if no user are provided, treat that as all users
	 * @param array $sort default ['from', 'desc'], sorts by the from field using descending order
	 * @param int $page default 0 which page to show (Yes, 0 is a number too, so we start at it)
	 * @param int $limit default 50 how many results per page
	 * 
	 * @return report[] $reports
	 */
	static function byUsers ( array $users = [], array $sort = null, int $page = 0, int $limit = 50 )
	{
		if ( $sort === null )
		{	$sort = ['from', 'desc']; }

		$ids = [];
		foreach ( $users as $user )
		{
			if ( !($user instanceof user) )
			{	throw new InvalidArgumentException ('reports::byUsers (), array provided must only contain user objects.'); }

			$ids [] = $user->id ();
		}

		var_dump ( $ids );

		$result = database(DB)->cache ('reports', 'id', '
			SELECT
				*

			FROM
				`reports`
		'. /* Dont include the where statement if no id's are provided */
		((isset ($ids [0])==true) ? ' WHERE `user` IN ('. substr ( str_repeat ( '?,', count ( $ids ) ), 0, -1 ) .')' : '') 
		.'

			ORDER BY
				`'. substr ( database(DB)->quote ($sort[0]), 1, -1 ) .'` '. substr ( database(DB)->quote (strtoupper ($sort[1]) ), 1, -1 ) .'

			LIMIT
				'. $page * $limit .','. $limit .'
		', $ids );

		$reports = [];
		$result->each ( function ( $row ) use ( &$users )
		{
			$reports [] = report::byId ( $row ['id'] );
		} );

		return $reports;
	}
}