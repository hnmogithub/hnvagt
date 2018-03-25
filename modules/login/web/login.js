r ( function ()
{
	$('#login').submit ( function ( e )
	{
		var data = new FormData ();
		data.append ( 'username', $('#login input[name="username"]').val () );
		data.append ( 'password', $('#login input[name="password"]').val () );

		$.ajax ({
			'url': 'ajax/login',
			'data': data,
			'processData': false,
			'contentType': false,

			'type': 'POST',
			'dataType': 'json',
			'success': function ( data )
			{
				if ( data.state == 0 )
				{
					
				}
				else
				{
					window.location.reload ();
				}
			}
		});

		e.preventDefault ();
		e.stopPropagation ();
		return false;
	} );
} );