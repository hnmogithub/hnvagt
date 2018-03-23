<?php
interface cacheWorker
{
    /**
     * Gets the cache entry matching parameters provided
     * 
     * @param string $table
     * @param mixed $id
     * 
     * @return array $row returns null if not found
     */
    public function get ( string $table, $id );

    /**
     * Sets the cache entry for the provided parameters
     * 
     * @param string $table
     * @param mixed $id
     * @param array $row
     */
    public function set ( string $table, $id, array $row );
}