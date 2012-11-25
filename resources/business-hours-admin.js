jQuery( document ).ready( function ( $ ) {

	$( "#exception_add" ).on( 'click', function ( e ) {

		var $item = $( ".exception_date" ).first().clone();

		var $count = $( '.exception_date' ).length;
		var $new_id = $count + 1;

		$item.attr( 'id', 'exception_' + $new_id );

		$item.find( '.exception_remove' ).attr( 'data-id', $new_id );
		$item.find( '.exception_number' ).val( $new_id );

		$item.hide().appendTo( '#exceptions_wrapper' ).slideDown( '150' );
	} );

	$( '#exceptions_wrapper' ).on( 'click', '.exception_remove', function ( e ) {

		var $id = $( this ).attr( 'data-id' );

		$( '.exception_remove_label' ).attr( 'disabled', 'disabled' );

		$( '#exception_' + $id ).slideUp( '150', function () {
			$( '#exception_' + $id ).remove();
		} );

		$( '.exception_remove_label' ).removeAttr( 'disabled' );

	} );


	function toggle_remove_buttons() {
		var $count = $( '.exception_date' ).length;
		if ( $count > 1 ) {
			$( '.exception_remove_label' ).show();
		} else {
			$( '.exception_remove_label' ).hide();
		}
	}

} );



