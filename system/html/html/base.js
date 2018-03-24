var save = [];

function r ( callback )
{
	if ( typeof (callback) == "function" )
	{
		save.push ( callback );
	}

	if ( window.$ )
	{
		for ( k in save )
		{
			setTimeout ( save [k], 0 );
		}
	}
	else
	{
		setTimeout ( function () { r () }, 100 );
	}
}
r ();