<?php
class user extends baseArray
{
    protected $data = [];

    /**
     * Constructor for the user class
     * 
     * @param int $id 
     */
    public function __construct ( int $id )
    {
        parent::__construct ( 'users', 'id' );

        
    }
}