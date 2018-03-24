$('#login').submit ( function ( e )
{
	var data = new FormData ();
	data.append ( 'username', $('#login input[name="username"]').val () );
	data.append ( 'password', $('#login input[name="password"]').val () );

	$.ajax ({
		'url': 'ajax/login',
		'data': data,
		'processData': false,

		'type': 'POST',
		'success': function ( data )
		{
			
		}
	});

	e.preventDefault ();
	e.stopPropagation ();
	return false;
} );