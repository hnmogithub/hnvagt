<?php 
error_reporting ( E_ALL );
ini_set("display_errors", 1);

include ( 'lib/schedule.php' );

schedule::prepare ();
schedule::run ();