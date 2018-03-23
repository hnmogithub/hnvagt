<?php
/**
 * Class used to handle running things in the right order
 */
class schedule
{
    /**
     * Contains the jobs
     */
    static private $jobs = [];

    /**
     * Contains the params
     */
    static private $param = [];

    /**
     * Adds a job to the scheduler
     * 
     * @param int $level
     * @param array $job [ object, function ]
     * @param array $param [ param1, param2 ] which parameters to pass along to the job
     */
    static public function jobAdd ( int $level, $job, $params )
    {
        if ( isset ( self::$jobs [ $level ] ) == false )
        {
            self::$jobs [ $level ] = [];
        }

        self::$jobs [ $level ][] = [ 'job' => $job, 'params' => $params ];
    }

    /**
     * Adds a parameter to the scheduler
     * 
     * @param string $name
     * @param mixed $parameter Can be anything, objects, strings whatever
     */
    static public function paramAdd ( string $name, $parameter )
    {
        self::$param [ $name ] = $parameter;
    }

    /**
     * Gets a parameter that have been added to the scheduler
     * 
     * @return mixed|null returns null if param hasn't been set
     */
    static public function paramGet ( string $name )
    {
        if ( isset ( self::$param [ $name ] ) == false ) { return null; }

        return self::$param [ $name ];
    }

    static public function run ()
    {
        ksort ( self::$jobs );

        foreach ( self::$jobs as $jId => $jobs )
        {
            foreach ( $jobs as $eId => $entry )
            {
                $params = [];
                foreach ( $entry ['params'] as $name )
                {
                    $params [] = self::paramGet ( $name );
                }

                call_user_func_array ( $entry ['job'], $params );
                unset ( self::$jobs [ $jId ][ $eId ] );
            }
        }
    }


    static public function prepare ()
    {
        $files = glob ('modules/*', GLOB_ONLYDIR );

        foreach ( $files as $file )
        {
            $file = basename ( $file );
            $path = $file .'/'. $file .'.php';

            if ( file_exists ( $path ) == true )
            {
                require_once ( $path );

                $file = '\\modules\\'. $file;
                $module = new $file ( self );
            }
        }
    }
}