<?php
class cacheMemory implements cacheWorker
{
    /**
     * Data Container
     * 
     * @var array $container
     */
    private $container = [];

    public function __construct ()
    { }

    public function get ( string $table, $id )
    {
        if ( isset ( $this->container [ $table ] ) == false || isset ( $this->container [ $table ][ $id ] ) == false )
        {   return null; }

        return $this->container [ $table ][ $id ];
    }

    public function set ( string $table, $id, array $row )
    {
        if ( isset ( $this->container [ $table ] ) == false )
        {   $this->container [ $table ] = []; }

        $this->container [ $table ][ $id ] = $row;
    }
}