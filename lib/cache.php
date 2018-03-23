<?php
class cache implements cacheWorker
{
	/**
	 * Contains the worker where the actually data is stored
	 * 
	 * @var cacheWorker $worker
	 */
	protected $worker = null;


	public function __construct ( $worker = null )
	{
		if ( $worker == null )
		{
			$worker = 'memory';
		}

		$worker = 'cache'. ucfirst ($worker);
		$this->worker = new $worker ();
	}

	public function get ( string $table, $id )
	{
		return $this->worker->get ( $table, $id );
	}

	public function set ( string $table, $id, array $row )
	{
		return $this->worker->set ( $table, $id, $row );
	}
}