<?php 
error_reporting ( E_ALL );
ini_set("display_errors", 1);
date_default_timezone_set('Europe/Copenhagen');

include ( 'lib/initialize.php' );

try
{
	schedule::prepare ();
	schedule::run ();
}
catch ( Exception $e )
{
	var_dump ($e);
}
