<?php
class cache
{
    /**
     * Contains the worker where the actually data is stored
     * 
     * @var cacheWorker $worker
     */
    protected $worker = null;


    public function __construct ( $worker = null )
    {
        if ( $worker = null )
        {
            $worker = 'memory';
        }

        $worker = 'cache'. ucfirst ($worker);
        $this->worker = new $worker ();
    }
}