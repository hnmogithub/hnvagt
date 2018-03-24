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
			if ( typeof (save[k] ) == 'function' )
			{
				setTimeout ( save [k], 0 );
				save [k] = undefined;
			}
		}
	}
	else
	{
		setTimeout ( function () { r () }, 100 );
	}
}
r ();