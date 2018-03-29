r ( function ()
{
	$(document).on ('keyup', function ( e )
	{
		console.log ( e.which, e.keyCode );
		switch ( e.which )
		{
			case 78: // N
				return $('#register-new-task').get(0).click ();
		}
	} );
} );