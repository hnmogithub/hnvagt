r ( function ()
{
	$(document).on ('keyup', function ( e )
	{
		if ( window.location.pathname == '/register/new/' )
		{	return; }

		switch ( e.which )
		{
			case 78: // N
				if ( $('*:focus').is ('input') == true ) { return; }
				return $('#register-new-task').get(0).click ();
		}
	} );
} );