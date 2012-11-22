jQuery( document ).ready( function ( $ ) {

	$( "body" ).on( 'click', '.business_hours_collapsible_handler', function ( e ) {
		e.preventDefault();
		$( ".business_hours_collapsible" ).slideToggle();
	} );


} );



