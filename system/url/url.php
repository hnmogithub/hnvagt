<?php namespace system;
use \schedule as schedule;
use \Response as Response;

class url 
{
	/**
	 * Used to store urls
	 */
	private $__urls = [];

	/**
	 * Tells if we got a job already running at the level
	 */
	private $__level = [];

	public function __construct ()
	{
		schedule::paramAdd ( 'url', $this );

		schedule::add ( schedule::$RUN_INIT, [ $this, 'file' ] );
	}

	/**
	 * Lets check if we are asking for a file, if we are, return that file
	 */
	public function file ()
	{
		$url = $_SERVER ['REQUEST_URI'];
		if ( isset ( $this->aliases [ $url ] ) == true )
		{	$url = $this->aliases [ $url ]; }

		$file = explode ( '.', basename ( $url ) );
		$extension = strtolower ( array_pop ( $file ) );

		switch ( $extension )
		{
			case 'css':
			case 'js':
				if ( file_exists ( '.'. $url ) == false )
				{	throw new Response ( 'File not found ('. $_SERVER ['REQUEST_URI'] .')', 404 ); }
				$type = [
					'css' => 'text/css;charset=utf-8',
					'js' => 'text/javascript;charset=utf-8'
				];

				header ( 'Content-Type:'. $type [ $extension ] );
				die ( file_get_contents ( '.'. $url ) );
		}
	}

	// -- 

	/**
	 * Contains uri aliases
	 * 
	 * @var array $aliases
	 */
	private $aliases = [];

	/**
	 * Generation of aliases for raw files
	 * 
	 * @param string $new 
	 * @param string $real 
	 */
	public function alias ( string $new, string $real )
	{
		$this->aliases [ $new ] = $real;
	}

	/**
	 * Runs a job on level if it matches the url, does not take query string into account
	 */
	public function request ( string $url, $level, $job, $params = [] )
	{
		/**
		 * We store them based on length of url as we want to match the most precise url first, then we go down until we get an exact match
		 */
		$len = strlen ( $url );
		if ( isset ( $this->__urls [ $len ] ) == false ) { $this->__urls [ $len ] = []; }

		$this->__urls [ $len ][] = [
			'level' => $level,
			'url' => $url,
			'job' => $job,
			'params' => $params
		];

		if ( isset ( $this->__level [ schedule::levelAt () ] ) == false )
		{
			$this->__level [ schedule::levelAt () ] = true;

			/**
			 * I could have made this into a function sitting on the object instead of using an anonymouse function however then the function would have to be a public function and the code being run in there, is for internal use only so it wouldnt really fit
			 */
			$urls =& $this->__urls;
			schedule::add ( schedule::levelAt (), function () use ( $urls )
			{
				unset ( $this->__level [ schedule::levelAt () ] );

				krsort ( $urls );
				foreach ( $urls as $lId => $jobs )
				{
					$found = false;
					foreach ( $jobs as $jId => $entry )
					{
						var_dump ( $entry );
						var_dump ( '/^'. preg_quote ( $entry ['url'], '/' ) .'/' );
						var_dump ( $_SERVER ['QUERY_STRING'] );
						if ( preg_match ('/^'. preg_quote ( $entry ['url'], '/' ) .'/', $_SERVER ['REQUEST_URI'] ) )
						{
							$found = true;
							unset ( $urls [ $lId ][ $jId ] );

							schedule::add ( $entry ['level'], $entry ['job'], $entry ['params'] );
						}
					}

					if ( $found === true )
					{
						break;
					}
				}

			} );
		}
	}
}