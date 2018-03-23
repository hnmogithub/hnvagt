<?php namespace modules;
use \schedule as schedule;

class login
{
    public function __construct ()
    {
        schedule::jobAdd ( 1, [ $this, 'run' ] );
    }

    public function run ()
    {
        database (DB)->cache ( 'users', 'id', '
            SELECT
                *
            FROM
                `users`
        ')->each ( function ( $row )
        {
            
        } );

        $user = users::byId ( 2 );
        var_dump ( $user );
    }
}