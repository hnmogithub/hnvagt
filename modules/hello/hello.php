<?php namespace modules;

class hello
{
	public function __construct ()
	{
		/** @var $url \system\url */
		$url = \schedule::paramGet ('url');

		$url->request ('/hello', 1, [ $this, 'doPrint' ] );
	}
}