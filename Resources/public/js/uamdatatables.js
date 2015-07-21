!function( $ ) {
	$.fn.uamdatatables = function( method ) {

		var settings,
			table;

		// Public methods
		var methods = {
			init: function( options ) {
				settings = $.extend( true, {}, $.fn.uamdatatables.defaults, options );

				return this.each(function() {
					var $this = $( this );

					$( ".filters input" ).keyup(function(event) {
						event.stopPropagation();
						table.api().draw();
					});

					$( ".filters input[type='date']" ).change(function( event ) {
						console.log( this.value );
						event.stopPropagation();
						table.api().draw();
					});

					$(".filters select").change(function(event) {
						event.stopPropagation();
						table.api().draw();
					});

					table = $( "table.table", this ).dataTable( $.extend(
						true,
						{
							ajax: {
								data: function( data ) {
									$( ".filters input, .filters select" ).each(function() {
										var name = $( this ).attr( "name" ),
											value = $( this ).val();

										if ( "checkbox" == $( this ).attr( "type" ) ) {
											value = $( this ).is( ":checked" ) ? $( this ).val() : 0;
										}

										if ("date" == $( this ).attr( "type" ) ) {
											value = $( this ).attr( "value" );
										}

										data[name] = value;
									} );
								}
							},
							initComplete: function( settings, json ) {
								$( this ).show();
							},
							language: {
								url: "/bundles/uamdatatables/vendor/datatables-plugins/i18n/" + settings.locale + ".json"
							}
							stateLoadParams: function( settings, data ) {
								$( ".filters input, .filters select", $this ).each(function() {
									var value = data[ $( this ).attr( "name" ) ];

									if ( $( this ).attr( "type" ) == "checkbox" ) {
										$( this ).attr( "checked", value );
									} else {
										$( this ).val( value );
									}
								});
							},
							stateSaveParams: function( settings, data ) {
								$( ".filters input, .filters select", $this ).each(function() {
									var name = $( this ).attr( "name" ),
										value = $( this ).attr( "type" ) == "checkbox"
											? ($( this ).is( ":checked" ) ? $( this ).val() : 0)
											: $( this ).val();

									data[ name ] = value;
								});
							}
						},
						settings
					) );
				});
			}
		};

		if ( methods[ method ] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ) );
		}
		else if ( typeof method === "object" || !method ) {
			return methods.init.apply( this, arguments );
		}
		else {
			$.error( "Method " +  method + " does not exist in $.uamdatatables." );
		}
	};

	$.fn.uamdatatables.defaults = {
		autoWidth: false,
		locale: "en",
		ordering: true,
		orderCellsTop: true,
		paging: true,
		processing: true,
		searching: true,
		serverSide: true,
		stripeClasses: []
	};
} ( window.jQuery );

$( document ).ready(function() {
	$( ".uamdatatables" ).uamdatatables( "undefined" === typeof uamdatatables ? {} : uamdatatables );
});
