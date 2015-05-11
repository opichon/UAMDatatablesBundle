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

					$(".filters select").change(function(event) {
						event.stopPropagation();
						table.api().draw();
					});

					table = $( "table.table", this ).dataTable( $.extend( true, {}, settings, {
						ajax: {
							data: function( data ) {
								$( ".filters input, .filters select" ).each(function() {
									var name = $( this ).attr( "name" ),
										value = $( this ).val();

									data[name] = value;
								} );
							}
						},
						initComplete: function( settings, json ) {
							$( this ).show();
						},
						language: {
							url: "/bundles/uamdatatables/vendor/datatables-plugins/i18n/" + settings.locale + ".json"
						},
					} ) );
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
		orderable: true,
		orderCellsTop: true,
		locale: "en",
		paging: true,
		processing: true,
		searching: false,
		serverSide: true,
		stripeClasses: []
	};
} ( window.jQuery );

$( document ).ready(function() {
	$( ".uamdatatables" ).uamdatatables( uamdatatables );
});
