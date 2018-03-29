r ( function ()
{
	$.getScript ('/modules/register_new/web/lib/anytime/anytime.5.2.0.min.js', function ()
	{
		AnyTime.picker ( 'register-new-date-from', {
			'format': '%Y-%m-%d %H:%i'
		} );
		AnyTime.picker ( 'register-new-date-to', {
			'format': '%Y-%m-%d %H:%i'
		} );
	} );

	$('#register-new').on ('click', function (e)
	{
		e.preventDefault ();
		e.stopPropagation ();

		return false;
	} );

	$('#register-new-close').on ( 'click', function ()
	{
		$('#register-new-backdrop').trigger ('click');
	} );

	$('#register-new-backdrop').on ('click', function ()
	{
		$('#register-new').css ({
			'right': '-51%',
			'animation-name': 'registerNewHide'
		});

		setTimeout ( function ()
		{
			window.location = '/register/';
		}, 500 );
	});

	$(document).on ('keypress', function (e)
	{
		if ( e.keyCode == 27 )
		{
			if ( $('*:focus').is ('input') == true )
			{
				return false;
			}

			$('#register-new-backdrop').trigger ('click');
		}
	});
} );