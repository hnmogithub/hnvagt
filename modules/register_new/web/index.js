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

	$('#register-new-backdrop').on ('click', function ()
	{
		
	});
} );