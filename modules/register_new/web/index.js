r ( function ()
{
	$.getScript ('/modules/register_new/web/lib/anytime/anytime.5.2.0.min.js', function ()
	{
		$(document).ready ( function ()
		{
			AnyTime.picker ( 'register-new-date-from', {
				'format': '%Y-%m-%d %H:%i'
			} );
			AnyTime.picker ( 'register-new-date-to', {
				'format': '%Y-%m-%d %H:%i'
			} );
		} );
	} );

	$.getScript ('/modules/register_new/web/lib/typeahead/typeahead.bundle.min.js', function ()
	{
		var bSources = new Bloodhound ({
			'datumTokenizer': Bloodhound.tokenizers.obj.whitespace("name"),
			'queryTokenizer': Bloodhound.tokenizers.whitespace,
			'prefetch': {
				'url': '/register/new/ajax/bSource',
			}
		});
		$('#register_new .source').typeahead ({
			highlight: true
		}, {
			name: 'sources',
			source: bSources
		});
	});

	$(document).ready ( function ()
	{
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

		$(document).on ('keyup', function ( e )
		{
			var target = e.target;
			if ( e.which == 27 && $(target).is ('input,textarea') )
			{
				
				var text = $(target).val ();
				if ( text == '' )
				{	$(target).blur (); }
				else
				{	$(target).val (''); }
			}
		});
	} );
} );