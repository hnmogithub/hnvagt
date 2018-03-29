r ( function ()
{
	$.getScript ('/modules/register_new/web/lib/anytime/anytime.5.2.0.min.js', function ()
	{
		$('input.datetime').on ( 'click', function ()
		{
			$(this).off ('click').AnyTime_picker ().focus ();
		} ).on ('blur', function ()
		{
			$(this).AnyTime_noPicker ();
		} );
	} );
} );