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


window.__keybinds = [];
r ( function ()
{
	$(document).ready ( function ()
	{
		var keybinds = $('*[data-keybind]');
		if ( keybinds.length > 0 )
		{
			keybinds.each ( function ()
			{
				var key = $(this).data ('keybind').toUpperCase ();

				var text = $(this).text ();
				text = text.replace ( RegExp('('+ key +')','i'), '<span class="underline">$1</span>' );
				if ( text !== $(this).text () )
				{	$(this).html ( text ); }
				else
				{
					$(this).prepend ('['+ key +'] ');
				}
				
				
				switch ( key )
				{
					case 'ESC':
						key = 27;
						break;
					default:
						key = key.charCodeAt ( 0 );
				}

				if ( window.__keybinds [ key ] !== undefined )
				{	console.warn ( 'Multiple keybinds on: '+ key +', overwriting current' ); }
				window.__keybinds [ key ] = this;
			} );

			$(document).on ('keydown', function ( e )
			{
				console.log ('1');
				if ( window.__keybinds [ e.which ] == undefined )
				{	return; }
				console.log ('2');

				if ( $('*:focus').is ('input,textarea') == true )
				{	return; }
				console.log ('3');

				$(window.__keybinds [ e.which ]).get(0).click ();
			} )
		}
	} );
} );