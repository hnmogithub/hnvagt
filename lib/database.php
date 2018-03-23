<?php
class database
{
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
     */
    public function query ( $query, $arguments = [] )
    {
        $smth = $this->handle->prepare ( $query );
        $smth->execute ( $arguments );

        return new databaseResult ( $smth );
    }
}