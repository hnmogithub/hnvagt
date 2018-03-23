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

/**
 * Gets a database, if database hasnt been connected yet, it will connect to it
 * 
 * @param string $database
 * @param string $host
 * @param string $user
 * @param string $pass
 * 
 * @return database $db
 */
function database ( string $db, string $host = 'localhost', string $user = 'hnvagt', string $pass = 'M@r!@db!' )
{
    static $databases = [];

    if ( isset ( $databases [ $db ] ) == false )
    {
        $databases [ $db ] = new database ( $host, $user, $pass, $db );
    }

    return $databases [ $db ];
}

/**
 * Gets the cache for the specifict database
 * 
 * @param string $database
 * 
 * @return cache $cache
 */
function cache ( string $db )
{
    static $caches = [];

    if ( isset ( $caches [ $db ] ) == false )
    {
        $caches [ $db ] = new cache ();
    }

    return $caches [ $db ];
}