<?php
spl_autoload_register ( function ( $class )
{
    if ( file_exists ( 'lib/'. $class .'.php' ) == true  )
    {
        require_once ( 'lib/'. $class .'.php' );

        return;
    }

    $mod = schedule::lastModule ();
    if ( $mod !== 'anonymouse' )
    {
        $mod = str_replace ( '\\', '/', $mod ) .'/'. $class .'.php';
        if ( file_exists ( $mod ) == true )
        {
            require_once ( $mod );

            return;
        }
    }
} );