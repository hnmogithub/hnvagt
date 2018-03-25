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
					var err = $('#login .error');
					clearTimeout ( err.data ('time') );

					err.animate ({
						'opacity': 1
					}, 100 );
					
					var time = setTimeout ( (function (err)
					{
						return function ()
						{
							err.animate ({
								'opacity': 0
							}, 100 );
						}
					})(err), 600 );

					err.data ('time',time);
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