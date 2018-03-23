<?php
class databaseResult
{
	/**
	 * Contains the statement returned by the pdo
	 * 
	 * @var PDOStatement $smth
	 */
	protected $smth = null;


	/**
	 * Constructor for database result
	 * 
	 * @param PDOStatement $smth
	 */
	public function __construct ( PDOStatement $smth )
	{
		$this->smth = $smth;
	}

	/**
	 * Returns each row from the statement provided and provides the callback with the row
	 * 
	 * @param function $callback
	 */
	public function each ( $callback )
	{
		while ( $row = $this->smth->fetch ( PDO::FETCH_ASSOC ) )
		{
			if ( call_user_func ( $callback, $row ) == false )
			{   break; }
		}
	}

	/**
	 * Fetches one row from the statement
	 * 
	 * @return array $row
	 */
	public function fetchOne ()
	{
		return $this->smth->fetch ( PDO::FETCH_ASSOC );
	}

	/**
	 * Fetches all rows from the statement
	 * 
	 * @return array[] $row
	 */
	public function fetchAll ()
	{
		return $this->smth->fetchAll ( PDO::FETCH_ASSOC );
	}

	/**
	 * Fetches the amount of rows returned by the statement
	 * 
	 * @return int $length
	 */
	public function length ()
	{
		return $this->smth->rowCount ();
	}
}