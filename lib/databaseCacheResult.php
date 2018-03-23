<?php
class databaseCacheResult extends databaseResult
{
    /**
     * Contains the statement returned by the pdo
     * 
     * @var PDOStatement $smth
     */
    protected $smth = null;

    /**
     * Contains which database the statement belongs to
     * 
     * @var string $database
     */
    private $database = null;

    /**
     * Contains which table our result belongs to
     * 
     * @var string $table
     */
    protected $table = null;

    /**
     * Contains which is the unique column in the table
     * 
     * @var string $id
     */
    protected $id = null;


    /**
     * Constructor for the cache result
     * 
     * @param string $database
     * @param string $table
     * @param string $id
     * @param PDOStatement $smth
     */
    public function __construct ( string $database, string $table, string $id, PDOStatement $smth )
    {
        $this->database = $database;
        $this->table = $table;
        $this->id = $id;

        $this->smth = $smth;
    }
    
    public function each ( $callback )
    {
        $skip = false;
        while ( $row = $this->smth->fetch ( PDO::FETCH_ASSOC ) )
        {
            // Cache it!
            cache ( $this->database )->set ( $this->table, $row [ $this->id ], $row );

            if ( call_user_func ( $callback, $row ) == false )
            {   break; }
        }
    }

    public function fetchOne ()
    {
        $row = $this->smth->fetch ( PDO::FETCH_ASSOC );

        // Cache it
        cache ( $this->database )->set ( $this->table, $row [ $this->id ], $row );
        return $row;
    }

    public function fetchAll ()
    {
        $rows = [];

        // No need to cache it since each () will cache it for us
        $this->each ( function ( $row ) use ( &$rows ) { $rows [] = $row; } );

        return $rows;
    }
}