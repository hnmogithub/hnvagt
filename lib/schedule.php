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
     * Used to store which level we are currently running at
     */
    static private $__at = 0;

    /**
     * Gets which level we are currently running at
     * 
     * @return int $level
     */
    static public function levelAt ()
    {
        return self::$__at;
    }

    /**
     * Used to store which module we are currently running
     */
    static private $__module = 'anonymouse';

    /**
     * Gets which module we are currently running
     * 
     * @param string $module
     */
    static public function lastModule ()
    {
        return self::$__module;
    }

    /**
     * Adds a job to the scheduler
     * 
     * @param int $level
     * @param array $job [ object, function ]
     * @param array $param [ param1, param2 ] which parameters to pass along to the job
     */
    static public function jobAdd ( int $level, $job, $params = [] )
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

    /**
     * Runs though all added jobs
     */
    static public function run ()
    {
        /**
         * Sorting and reseting on each pass incase more jobs were added by the jobs we just ran
         */
        ksort ( self::$jobs );
        reset ( self::$jobs );

        while ( list ( $jId, $jobs ) = each ( self::$jobs ) )
        {
            self::$__at = $jId;

            while ( list ( $eId, $entry ) = each ( $jobs ) )
            {
                $params = [];
                foreach ( $entry ['params'] as $name )
                {   $params [] = self::paramGet ( $name ); }

                if ( count ( $entry ['job'] ) > 1 )
                {   self::$__module = getclass ( $entry ['job'][0] ); }
                else
                {   self::$__module = 'anonymouse'; }


                call_user_func_array ( $entry ['job'], $params );
                unset ( self::$jobs [ $jId ][ $eId ] );

                ksort ( self::$jobs [ $jId ] );
                reset ( self::$jobs [ $eId ] );
            }

            if ( count ( self::$jobs [ $jId ] ) < 1 )
            {   unset ( self::$jobs [ $jId ] ); }

            ksort ( self::$jobs );
            reset ( self::$jobs );
        }
    }


    /**
     * Function that prepares the scheduler to run
     * loads all modules from /system/ and /modules/
     */
    static public function prepare ()
    {
        self::loadDirectory ('system');
        self::loadDirectory ('modules');
    }

    /**
     * Loads all modules in a certain directory
     * 
     * @param string $directory which directory to load
     * @param string $namespace which namespace are the objects located in, if none provided, we use directory name as namespace
     */
    static public function loadDirectory ( string $directory, string $namespace = null )
    {
        $files = glob ( $directory .'/*', GLOB_ONLYDIR );
        if ( $namespace === null ) { $namespace = basename ( $directory ); }

        foreach ( $files as $file )
        {
            $file = basename ( $file );
            $path = $directory .'/'. $file .'/'. $file .'.php';

            if ( file_exists ( $path ) == true )
            {
                require_once ( $path );

                $file = '\\'. $namespace .'\\'. $file;
                if ( class_exists ( $file ) == true )
                {
                    $module = new $file ();
                    unset ( $module );
                }
            }
        }
    }
}