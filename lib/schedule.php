<?php
/**
 * Class used to handle running things in the right order
 */
class schedule
{
	/**
	 * Default run levels
	 */
	static public $RUN_INIT = 0;
	static public $RUN_EARLY = 10;
	static public $RUN_MIDDLE = 20;
	static public $RUN_HTML = 30;
	static public $RUN_LATE = 40;


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
	 * Contains the namespaces of modules loaded
	 */
	static public $namespaces = [];

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
	static public function add ( int $level, $job, $params = [] )
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
				{   self::$__module = get_class ( $entry ['job'][0] ); }
				else
				{   self::$__module = 'anonymouse'; }


				call_user_func_array ( $entry ['job'], $params );
				unset ( self::$jobs [ $jId ][ $eId ] );

				ksort ( self::$jobs [ $jId ] );
				reset ( self::$jobs [ $jId ] );
			}

			if ( count ( self::$jobs [ $jId ] ) < 1 )
			{   unset ( self::$jobs [ $jId ] ); }

			ksort ( self::$jobs );
			reset ( self::$jobs );
		}
	}


	/**
	 * Function that prepares the scheduler to run
	 * loads all modules from /system/
	 */
	static public function prepare ()
	{
		self::loadDirectory ('system');
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

			self::load ( $path, $namespace );
		}
	}

	/**
	 * Loads a module
	 * 
	 * @param string $file path to file
	 * @param string $namespace which namespace is the class inside the file in
	 */
	static public function load ( string $file, string $namespace = null )
	{
		if ( file_exists ( $file ) == false )
		{	throw new InvalidArgumentException ( 'schedule::load (), file provided does not exist ('. $file .')' ); }

		if ( $namespace == null )
		{	$namespace = basename ( dirname ( $file ) ); }

		require_once ( $file );

		$class = '\\'. $namespace .'\\'. substr ( basename ( $file ), 0, -4 );
		if ( class_exists ( $class ) == false )
		{	throw new Exception ( 'schedule::load (), class not found in file provided ('. $file .')' ); }

		$module = new $class ();
		self::$namespaces [ get_class ( $module ) ] = $namespace;
	}
}