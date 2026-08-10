/**
 * Checkout field builder ordering.
 *
 * Adds drag-and-drop ordering to the field builder table. The order of the
 * rows in the submitted form is the configured checkout order, so dragging a
 * row only changes its DOM position; the plugin saves the new order with the
 * next form submission. No dedicated request mechanism is used.
 */
( function () {
	'use strict';

	var tbody = document.getElementById( 'scfw-fields' );

	if ( ! tbody ) {
		return;
	}

	var dragging = null;

	tbody.addEventListener( 'dragstart', function ( event ) {
		if ( ! event.target.classList.contains( 'scfw-handle' ) ) {
			return;
		}

		dragging = event.target.closest( 'tr' );

		if ( ! dragging ) {
			return;
		}

		dragging.classList.add( 'scfw-dragging' );
		event.dataTransfer.effectAllowed = 'move';
		event.dataTransfer.setData( 'text/plain', dragging.dataset.row );
	} );

	tbody.addEventListener( 'dragover', function ( event ) {
		var row = event.target.closest( 'tr' );

		if ( ! row || row === dragging ) {
			return;
		}

		event.preventDefault();

		var rect        = row.getBoundingClientRect();
		var placeAfter  = event.clientY - rect.top > rect.height / 2;

		if ( placeAfter ) {
			row.after( dragging );
		} else {
			row.before( dragging );
		}
	} );

	tbody.addEventListener( 'drop', function ( event ) {
		event.preventDefault();
	} );

	tbody.addEventListener( 'dragend', function () {
		if ( dragging ) {
			dragging.classList.remove( 'scfw-dragging' );
			dragging = null;
		}
	} );
}() );