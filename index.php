<?php 
error_reporting ( E_ALL );
ini_set("display_errors", 1);

include ( 'lib/initialize.php' );
var_dump ( $_SERVER );

try
{
	schedule::prepare ();
	schedule::run ();
}
catch ( Exception $e )
{
	var_dump ($e);
}
