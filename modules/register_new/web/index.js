r ( function ()
{
	$.getScript ('/modules/register_new/web/lib/anytime/anytime.5.2.0.min.js', function ()
	{
		AnyTime.picker ( 'date-from', {
			'format': '%Y-%m-%d %T'
		} );
	} );
} );