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
        database ('vagt')->cache ( 'users', 'id', '
            SELECT
                *
            FROM
                `users`
        ')->each ( function ( $row )
        {
            var_dump ( $row );
        } );
    }
}