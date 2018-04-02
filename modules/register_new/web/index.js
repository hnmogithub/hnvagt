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
				empty: '<div class="warning">Unable to use this selection</div>'
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
							var result = [];
							result.push ({'id': -10, 'name': 'Create new'});
							result.push ({'id': -11, 'name': 'Create alias'});
							sync (result);
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
		type.on ('typeahead:selected', function ( e, selected )
		{
			if ( selected.id == -10 )
			{
				$(this).typeahead('val','');

				$('#register-new-input h3').text ('Create new Type');
				$('#register-new-input').css ({
					'visibility': 'visible',
					'opacity': 1,
					'animation-name': 'registerNewInputShow'
				});

				$('#register-new-input').off('submit').on ('submit', function ( e )
				{
					console.log ( 'here' );
					var data = new FormData ();
					data.append ( 'name', $(this).find ('input[type="text"]').val () );

					$.ajax ({
						'url': '/register/new/ajax/nType',
						'data': data,
						'processData': false,
						'contentType': false,

						'type': 'POST',
						'dataType': 'json',
						'success': function ( data )
						{
							console.log ( data );
						}
					});

					e.preventDefault ();
					e.stopPropagation ();
					return false;
				});
			}
		});
	});

	$(document).ready ( function ()
	{
		$('#register-new-input').on ( 'click', function ()
		{
			$(this).css ({
				'opacity': 0,
				'animation-name': 'registerNewInputHide'
			});
			setTimeout ( function ()
			{
				$('#register-new-input').css ('visibility','hidden');
			}, 500 );
		} );
		$('#register-new-input > div').on ('click', function ( e )
		{
			e.preventDefault();
			e.stopPropagation();
			return false;
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