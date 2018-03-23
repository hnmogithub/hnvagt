<?php
class database
{
	/**
	 * Stores which database this is
	 */
	private $database = null;

	/**
	 * Contains the database handle
	 * 
	 * @var $handle resource
	 */
	private $handle = null;

	/**
	 * constructor, takes an array as argument containing all relevant 
	 * 
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $database
	 */
	public function __construct ( string $host, string $user, string $pass, string $database )
	{
		$this->handle = new PDO('mysql:host='. $host .';dbname='. $database, $user, $pass );
		$this->handle->setAttribute ( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$this->handle->setAttribute ( PDO::ATTR_EMULATE_PREPARES, false );
		$this->handle->exec ( 'SET NAMES "utf8";' );
		$this->handle->exec ( 'SET sql_mode=(SELECT REPLACE(@@sql_mode,"ONLY_FULL_GROUP_BY",""));' );

		$this->database = $database;
	}

	/**
	 * Escapes the string provided
	 * 
	 * @param string $string
	 * 
	 * @return string $string
	 */
	public function quote ( string $string )
	{
		return $this->handle->quote ( $string );
	}

	/**
	 * Runs a query against the database
	 * 
	 * @param string $query
	 * @param array $arguments
	 * 
	 * @return databaseResult $result
	 */
	public function query ( $query, $arguments = [] )
	{
		$smth = $this->handle->prepare ( $query );
		$smth->execute ( $arguments );

		return new databaseResult ( $smth );
	}

	/**
	 * Caches a query result
	 * 
	 * @param string $table
	 * @param string $identifier
	 * @param string $query
	 * @param array $arguments
	 * 
	 * @return databaseResult $result
	 */
	public function cache ( string $table, string $id, string $query, array $arguments = [] )
	{
		$smth = $this->handle->prepare ( $query );
		$smth->execute ( $arguments );

		return new databaseCacheResult ( $this->database, $table, $id, $smth );
	}

	/**
	 * Gets the last inserted id
	 * 
	 * @return int $id
	 */
	public function lastId ()
	{
		return $this->handle->lastInsertId ();
	}
}