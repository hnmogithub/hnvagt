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


var window.__keybinds = [];
r ( function ()
{
	$(document).ready ( function ()
	{
		var keybinds = $('*[data-keybind]');
		if ( keybinds.length > 0 )
		{
			keybinds.each ( function ()
			{
				var key = $(this).data ('keybind');
				switch ( key )
				{
					case 'ESC':
						key = 27;
						break;
					default:
						key = key.charCodeAt ( 0 );
				}

				__keybinds [ key ] = this;
			} );

			$(document).on ('keydown', function ( e )
			{
				console.log ('1');
				if ( __keybinds [ e.which ] == undefined )
				{	return; }
				console.log ('2');

				if ( $('*:focus').is ('input,textarea') == true )
				{	return; }
				console.log ('3');

				$(__keybinds [ e.which ]).get(0).click ();
			} )
		}
	} );
} );