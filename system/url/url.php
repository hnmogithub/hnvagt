<?php namespace system;
use \schedule as schedule;
use \Response as Response;

/**
 * Handles things related to urls
 */
class url 
{
	/**
	 * Used to store urls
	 * 
	 * @var array $__urls
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

		var_dump ( $extension );

		switch ( $extension )
		{
			case 'css':
			case 'js':
			case 'png':
				if ( file_exists ( '.'. $url ) == false )
				{	throw new Response ( 'File not found ('. $_SERVER ['REQUEST_URI'] .')', 404 ); }
				$type = [
					'png' => 'image/png',
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
	 * 
	 * @param string $url
	 * @param int $level
	 * @param array $job
	 * @param array $params optional
	 */
	public function request ( string $url, int $level, array $job, array $params = [] )
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
			 * This aproach is stupidly overcomplex, the original idea was to have only the first responder per level being run, however, why?
			 * 
			 * I could have made this into a function sitting on the object instead of using an anonymouse function however then the function would have to be a public function and the code being run in there, is for internal use only so it wouldnt really fit
			 */
			$urls =& $this->__urls;
			schedule::add ( schedule::levelAt (), function () use ( &$urls )
			{
				unset ( $this->__level [ schedule::levelAt () ] );

				krsort ( $urls );
				foreach ( $urls as $lId => $jobs )
				{
					foreach ( $jobs as $jId => $entry )
					{
						$url = str_replace ( '/', '\\/', $entry ['url'] );
						if ( substr ( $url, 0, 1 ) !== '^' )
						{	$url = '^'. $url; }
						$url = '/'. $url .'/';

						if ( preg_match ( $url, $_SERVER ['REQUEST_URI'] ) )
						{
							unset ( $urls [ $lId ][ $jId ] );

							schedule::add ( $entry ['level'], $entry ['job'], $entry ['params'] );
						}
					}
				}

			} );
		}
	}
}