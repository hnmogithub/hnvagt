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
		// -- Sources
		var bSources = new Bloodhound ({
			'datumTokenizer': Bloodhound.tokenizers.obj.whitespace('name', 'id'),
			'queryTokenizer': Bloodhound.tokenizers.whitespace,
			'prefetch': {
				'url': '/register/new/ajax/bSource',
				'cache': false,
			}
		});
		bSources.initialize ();

		$('#register-new .source input').typeahead ({
			highlight: true,
			hint: true,
			minLength: 0,
		},{
			name: 'sources',
			source: function ( q, sync )
			{
				if ( q === '' )
				{	sync ( bSources.index.all () ); }
				else
				{
					bSources.search ( q, sync );
				}
			},

			display: 'name',
			templates: {
				suggestion: function ( data )
				{
					return '<div><div class="id">'+ data.id +'</div><div class="name">'+ data.name +'</div></div>';
				}
			}
		});
		$('#register-new .source input').on ('focus', function () { $(this).typeahead ('open') });

		// -- Types
		var bTypes = new Bloodhound ({
			'datumTokenizer': Bloodhound.tokenizers.obj.whitespace('name', 'id'),
			'queryTokenizer': Bloodhound.tokenizers.whitespace,
			'prefetch': {
				'url': '/register/new/ajax/bType?other=true',
				'cache': false,
			}
		});
		bTypes.initialize ();

		$('#register-new .type input').typeahead ({
			highlight: true,
			hint: true,
			minLength: 0,
		},{
			name: 'types',
			source: function ( q, sync )
			{
				if ( q === '' )
				{	sync ( bTypes.index.all () ); }
				else
				{
					bTypes.search ( q, sync );
				}
			},

			display: 'name',
			templates: {
				suggestion: function ( data )
				{
					return '<div><div class="id">'+ data.id +'</div><div class="name">'+ data.name +'</div></div>';
				},
				empty: '<div class="text-center"><a href="javascript:void(0);">Create new</a></div><div class="text-center"><a href="javascript:void(0);">Create alias</a></div>'
			}
		});
		$('#register-new .type input').on ('focus', function () { $(this).typeahead ('open') });
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