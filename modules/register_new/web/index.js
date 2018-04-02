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

		var source = $('#register-new .source input');
		source.typeahead ({
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
				},
				empty: '<div class="warning">Unable to use this selection</div>';
			}
		});
		source.on ('focus', function () { $(this).typeahead ('open') });

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

		var type = $('#register-new .type input');
		type.typeahead ({
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
					bTypes.search ( q, function ( result )
					{
						if ( result.length > 0 )
						{
							sync ( result );
						}
						else
						{
							sync ([
								{'id': -10, 'name': 'Create new'},
								{'id': -11, 'name': 'Create alias'}
							]);
						}
					});

					console.log (sync());
				}
			},

			display: 'name',
			templates: {
				suggestion: function ( data )
				{
					if ( data.id < 0 ) { data.id = "&nbsp;"; }

					return '<div><div class="id">'+ data.id +'</div><div class="name">'+ data.name +'</div></div>';
				}
			}
		});
		type.on ('focus', function () { $(this).typeahead ('open') });
		type.on ('typeahead:selected', function (a,b,c)
		{
			console.log ( a,b,c );
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