<?php namespace modules;

class login
{
    public function __construct ()
    {
        \schedule::jobAdd ( 1, [ $this, 'run' ] );
    }

    public function run ()
    {
        echo \schedule::lastModule ();
    }
}