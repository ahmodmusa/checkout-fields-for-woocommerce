/**
 * Admin order inline checkout fields editor.
 *
 * @package SCFW
 */
( function( $ ) {
	'use strict';

	$( function() {
		$( document ).on( 'click', '.scfw-edit-order-fields-btn', function( e ) {
			e.preventDefault();
			var $container = $( this ).closest( '.scfw-admin-order-fields' );
			$container.find( '.scfw-admin-order-fields-view' ).hide();
			$container.find( '.scfw-admin-order-fields-edit' ).show();
			$container.find( '.scfw-edit-order-fields-btn' ).hide();
			$container.find( '.scfw-cancel-order-fields-btn' ).show();
		} );

		$( document ).on( 'click', '.scfw-cancel-order-fields-btn', function( e ) {
			e.preventDefault();
			var $container = $( this ).closest( '.scfw-admin-order-fields' );
			$container.find( '.scfw-admin-order-fields-edit' ).hide();
			$container.find( '.scfw-admin-order-fields-view' ).show();
			$container.find( '.scfw-cancel-order-fields-btn' ).hide();
			$container.find( '.scfw-edit-order-fields-btn' ).show();
		} );
	} );
} )( jQuery );
