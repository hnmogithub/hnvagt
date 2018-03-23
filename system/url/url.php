<?php namespace system;

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
        \schedule::paramAdd ( 'url', $this );
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

        if ( isset ( $this->__level [ \schedule::levelAt () ] ) == false )
        {
            $this->__level [ \schedule::levelAt () ] = true;

            /**
             * I could have made this into a function sitting on the object instead of using an anonymouse function however then the function would have to be a public function and the code being run in there, is for internal use only so it wouldnt really fit
             */
            $urls =& $this->__urls;
            \schedule::jobAdd ( \schedule::levelAt (), function () use ( $urls )
            {
                unset ( $this->__level [ \schedule::levelAt () ] );

                krsort ( $urls );
                foreach ( $urls as $lId => $jobs )
                {
                    $found = false;
                    foreach ( $jobs as $jId => $entry )
                    {
                        if ( preg_match ('/^'. $entry ['url'] .'/', $_SERVER ['QUERY_STRING'] ) )
                        {
                            $found = true;
                            unset ( $urls [ $lId ][ $jId ] );

                            \schedule::jobAdd ( $entry ['level'], $entry ['job'], $entry ['params'] );
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