r ( function ()
{
	$(document).on ('keyup', function ( e )
	{
		switch ( e.which )
		{
			case 78: // N
				return $('#register-new-task').trigger ('click');
		}
	} );
} );