<?php
spl_autoload_register ( function ( $class )
{
    if ( file_exists ( '/lib/'. $class .'.php' ) == true  )
    {
        
    }
} );