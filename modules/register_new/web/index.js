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
		/**
		 * Bloodhound and Typeahead for the sources input
		 */
		(function ()
		{
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
			source.data ('bloodhound', bSources);
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
			source.on ('blur', function ()
			{
				var that = this;
				$(this).data ('bloodhound').search ( $(this).val (), function ( result )
				{
					if ( result.length == 0 || result[0].name !== $(that).typeahead ('val') )
					{
						$(that).typeahead ('val', '');
					}
				} );
			});
		})();

		/**
		 * Bloodhound and Typeahead for the types input
		 * 
		 * NOTE: We might need to write a remote for bloodhound here depending on the amount thats gonna be added to type, preloading every single type works when there's not that many, however as this increases it might be an issue
		 */
		(function ()
		{
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
			type.data ('bloodhound', bTypes);
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
					}
				},
				limit: 10,
	
				display: 'name',
				templates: {
					suggestion: function ( data )
					{
						if ( data.id < 0 ) { data.id = "&nbsp;"; }
	
						var string = '<div title="Created by: '+ data.created_by +', '+ data.created_at +' "><div class="id">'+ data.id +'</div><div class="name">'+ data.name +'</div>';
						if ( data.other == 0 )
						{	string += '<div class="other">&nbsp;</div>'; }
						string += '</div>';
	
						return string;
					}
				}
			});
			type.on ('focus', function () { $(this).typeahead ('open') });
			type.on ('typeahead:selected', function ( e, selected )
			{
				if ( selected.id == -10 )
				{
					$(this).typeahead ('val','');
	
					$('#register-new-input h3').text ('Create new Type');
					$('#register-new-input').css ({
						'visibility': 'visible',
						'opacity': 1,
						'animation-name': 'registerNewInputShow'
					});
	
					$('#register-new-input').off('submit').on ('submit', function ( e )
					{
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
								bTypes.add ( data );
	
								var input = $('#register-new .type input');
								input.typeahead ('val', data.name );
								input.typeahead ('open');
								input.focus ();
	
								$('#register-new-input').trigger ('click');
							}
						});
	
						e.preventDefault ();
						e.stopPropagation ();
						return false;
					});
	
					$('#register-new-input input[type="submit"]').on ('click', function ()
					{
						$(this).trigger ('submit');
					});
	
					setTimeout ( function ()
					{
						$('#register-new-input input[type="text"]').focus ();
					}, 0 );
				}
			});
			type.on ('blur', function ()
			{
				var that = this;
				$(this).data ('bloodhound').search ( $(this).val (), function ( result )
				{
					if ( result.length == 0 || result[0].name !== $(that).typeahead ('val') )
					{
						$(that).typeahead ('val', '');
					}
				} );
			});
		})();

		/**
		 * Bloodhound and Typeahead for the customers input
		 */
		(function ()
		{
			var bCustomers = new Bloodhound ({
				'datumTokenizer': Bloodhound.tokenizers.obj.whitespace('name', 'id'),
				'queryTokenizer': Bloodhound.tokenizers.whitespace,
				'remote': {
					'transport': function ( options, c, onSuccess, onError )
					{
						var data = new FormData ();
						data.append ('source', $('#register-new .source input[name="source"]').typeahead ('val') );
						data.append ('type', $('#register-new .type input[name="type"]').typeahead ('val') );
	
						options ['data'] = data;
						options ['processData'] = false;
						options ['contentType'] = false,
						options ['type'] = 'POST',
	
						options ['success'] = onSuccess;
						options ['error'] = function ( r, t, e )
						{	onError ( e ); };
	
						$.ajax (options);
					},
					'url': '/register/new/ajax/bCustomer?search=%QUERY',
					'wildcard': "%QUERY",
					'cache': false,
				},
				'prefetch': {
					'url': '/register/new/ajax/bCustomer?prefetch=true',
					'cache': false,
				},
			});
			bCustomers.initialize ();
	
			var customer = $('#register-new .customer input');
			customer.data ('bloodhound', bCustomers);
			customer.typeahead ({
				highlight: true,
				hint: true,
				minLength: 0,
			},{
				name: 'customers',
				source: function ( q, sync )
				{
					if ( q === '' )
					{	sync ( bCustomers.index.all () ); }
					else
					{
						bCustomers.search ( q, function ( result )
						{
							if ( result.length > 0 )
							{
								sync ( result );
							}
							else
							{
								var result = [];
								result.push ({'id': -10, 'name': 'Create new'});
								sync (result);
							}
						});
					}
				},
				limit: 10,
	
				display: 'name',
				templates: {
					suggestion: function ( data )
					{
						if ( data.id < 0 ) { data.id = "&nbsp;"; }
	
						var string = '<div title="Created by: '+ data.created_by +', '+ data.created_at +' "><div class="id">'+ data.id +'</div><div class="name">'+ data.name +'</div>';
						string += '</div>';
	
						return string;
					}
				}
			});
			customer.on ('focus', function () { $(this).typeahead ('open') });
			customer.on ('typeahead:selected', function ( e, selected )
			{
				if ( selected.id == -10 )
				{
					$(this).typeahead ('val','');
	
					$('#register-new-input h3').text ('Create new Customer');
					$('#register-new-input').css ({
						'visibility': 'visible',
						'opacity': 1,
						'animation-name': 'registerNewInputShow'
					});
	
					var select = $(document.createElement ('select'));
					select.css ('width', '100%');
					select.attr ('name', 'type');
					select.attr ('id', 'customerType');
					$(select).insertBefore ('#register-new-input input[type="text"]');
	
					$.ajax ({
						'url': '/register/new/ajax/customerTypes',
						'type': 'GET',
	
						'dataType': 'json',
						'success': function ( data )
						{
							var select = $('#customerType');
							for ( var i in data )
							{
								var option = $(document.createElement ('option'));
								option.attr ('value', data [i].id );
								option.text ( data [i].name );
								select.append ( option );
							}
						},
						'error': function ( r, t, e )
						{
							$('#customerType').remove ();
	
							console.warn ( r,t,e );
						}
					})
	
					$('#register-new-input').off('submit').on ('submit', function ( e )
					{
						var data = new FormData ();
						data.append ( 'name', $(this).find ('input[type="text"]').val () );
						data.append ( 'type', $('#customerType').val () );
	
						$.ajax ({
							'url': '/register/new/ajax/nCustomer',
							'data': data,
							'processData': false,
							'contentType': false,
	
							'type': 'POST',
							'dataType': 'json',
							'success': function ( data )
							{
								if ( data.error !== undefined )
								{	return alert ( data.error ); }
	
								bCustomers.add ( data );
	
								var input = $('#register-new .customer input');
								input.typeahead ('val', data.name );
								input.typeahead ('open');
								input.focus ();
	
								$('#register-new-input').trigger ('click');
								$('#customerType').remove ();
							}
						});
	
						e.preventDefault ();
						e.stopPropagation ();
						return false;
					});
	
					$('#register-new-input input[type="submit"]').on ('click', function ()
					{
						$(this).trigger ('submit');
					});
	
					setTimeout ( function ()
					{
						$('#register-new-input input[type="text"]').focus ();
					}, 0 );
				}
				else
				{
					
				}
			});
			customer.on ('blur', function ()
			{
				$('#register-new .customer_user input[name="customer_user"]').data ('bloodhound').search ('', function () {});


				var that = this;
				$(this).data ('bloodhound').search ( $(this).val (), function ( result )
				{
					if ( result.length == 0 || result[0].name !== $(that).typeahead ('val') )
					{
						$(that).typeahead ('val', '');
					}
				} );
			});
		})();
		
		/**
		 * Bloodhound and Typeahead for the customer users input
		 */
		(function ()
		{
			var bUsers = new Bloodhound ({
				'datumTokenizer': Bloodhound.tokenizers.obj.whitespace('name', 'id'),
				'queryTokenizer': Bloodhound.tokenizers.whitespace,

				'prefetch': {
					'url': '/register/new/ajax/bCustomerUser?prefetch=true',
					'cache': false,
				},
				'remote': {
					'transport': function ( options, c, onSuccess, onError )
					{
						var data = new FormData ();

						var val = $('#register-new .type input[name="type"]').typeahead ('val');
						$('#register-new .type input[name="type"]').data('bloodhound').search ( val, function ( result )
						{
							if ( result [0] == undefined )
							{	val = null; }
							else
							{	val = result [0].id; }
						} );
						data.append ('type', val );

						data.append ('customer', $('#register-new .customer input[name="customer"]').typeahead ('val') );
	
						options ['data'] = data;
						options ['processData'] = false;
						options ['contentType'] = false,
						options ['type'] = 'POST',
	
						options ['success'] = onSuccess;
						options ['error'] = function ( r, t, e )
						{	onError ( e ); };
	
						$.ajax (options);
					},
					'url': '/register/new/ajax/bCustomerUser?search=%QUERY',
					'wildcard': "%QUERY",
					'cache': false,
				},
			});
			bUsers.initialize ();
	
			var users = $('#register-new .customer_user input');
			users.data ('bloodhound', bUsers);
			users.typeahead ({
				highlight: true,
				hint: true,
				minLength: 0,
			},{
				name: 'customers_users',
				source: function ( q, sync )
				{
					if ( q === '' )
					{	sync ( bUsers.index.all () ); }
					else
					{
						bUsers.search ( q, function ( result )
						{
							if ( result.length > 0 )
							{
								sync ( result );
							}
							else
							{
								var result = [];
								result.push ({'id': -10, 'name': 'Create new'});
								sync (result);
							}
						});
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
			users.on ('focus', function ()
			{
				$(this).typeahead ('open');
			});
			users.on ('blur', function ()
			{
				var that = this;
				if ( $(this).typeahead ('val') == '' )
				{
					$(this).data ('bloodhound').search ('system user', function ( result )
					{
						if ( result [0] !== undefined )
						{
							$(that).typeahead ( 'val', result [0].name );
						}
					});
					return;
				}

				$(this).data ('bloodhound').search ( $(this).val (), function ( result )
				{
					if ( result.length == 0 || result[0].name !== $(that).typeahead ('val') )
					{
						$(that).typeahead ('val', '');
					}
				} );
			});
		})();

		/**
		 * Bloodhound and Typeahead for the location input
		 */
		(function ()
		{
			var bLocation = new Bloodhound ({
				'datumTokenizer': Bloodhound.tokenizers.obj.whitespace('name', 'id'),
				'queryTokenizer': Bloodhound.tokenizers.whitespace,
				'prefetch': {
					'url': '/register/new/ajax/bLocation?prefetch=true',
					'cache': false,
				},
				'remote': {
					'transport': function ( options, c, onSuccess, onError )
					{
						var data = new FormData ();

						var val = $('#register-new .customer input[name="customer"]').typeahead ('val');
						$('#register-new .customer input[name="customer"]').data('bloodhound').search ( val, function ( result )
						{
							if ( result [0] == undefined )
							{	val = null; }
							else
							{	val = result [0].id; }
						} );
						data.append ('type', val );
	
						options ['data'] = data;
						options ['processData'] = false;
						options ['contentType'] = false,
						options ['type'] = 'POST',
	
						options ['success'] = onSuccess;
						options ['error'] = function ( r, t, e )
						{	onError ( e ); };
	
						$.ajax (options);
					},
					'url': '/register/new/ajax/bLocation?search=%QUERY',
					'wildcard': "%QUERY",
					'cache': false,
				}
			});
			bLocation.initialize ();
	
			var location = $('#register-new .location input');
			location.data ('bloodhound', bLocation);
			location.typeahead ({
				highlight: true,
				hint: true,
				minLength: 0,
			},{
				name: 'location',
				source: function ( q, sync )
				{
					if ( q === '' )
					{	sync ( bLocation.index.all () ); }
					else
					{
						bLocation.search ( q, sync );
					}
				},
	
				display: 'name',
				templates: {
					suggestion: function ( data )
					{
						return '<div><div class="id">&nbsp;</div><div class="name">'+ data.name +'</div></div>';
					}
				}
			});
			location.on ('focus', function () { $(this).typeahead ('open') });
		})();
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
				{
					$(target).val ('');
					$(target).typeahead ('val','');
				}
			}
		});
	} );
} );