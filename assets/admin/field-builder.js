/**
 * Checkout field builder interactions.
 *
 * Supports the reference-accurate table layout, modal/drawer editor, inline
 * enabled switches, and HTML5 drag-and-drop row reordering.
 */
( function () {
	'use strict';

	var fieldsView = document.getElementById( 'scfw-fields-view' );
	var tbody      = document.getElementById( 'scfw-fields-tbody' );
	var backdrop   = document.getElementById( 'scfw-editor-backdrop' );
	var modalClose = document.getElementById( 'scfw-modal-close' );
	var modalBody  = document.getElementById( 'scfw-modal-body' );
	var topAddBtn  = document.getElementById( 'scfw-top-add-btn' );

	if ( ! fieldsView || ! tbody ) {
		return;
	}

	var dragging = null;

	var DETAILS_TYPES = [ 'radio', 'select', 'text', 'textarea' ];

	function hasDetails( type ) {
		return -1 !== DETAILS_TYPES.indexOf( type );
	}

	function isChoice( type ) {
		return 'radio' === type || 'select' === type;
	}

	function isText( type ) {
		return 'text' === type || 'textarea' === type;
	}

	function isTextarea( type ) {
		return 'textarea' === type;
	}

	function fieldRowOf( element ) {
		return element.closest( '.scfw-field-row' );
	}

	function editorOf( rowIdx ) {
		return document.getElementById( 'scfw-field-editor-' + rowIdx );
	}

	function openModalForEditor( rowIdx ) {
		if ( ! backdrop || ! modalBody ) {
			return;
		}

		modalBody.querySelectorAll( '.scfw-field-editor' ).forEach( function ( el ) {
			el.hidden = true;
		} );

		var addPanel = modalBody.querySelector( '.scfw-add-field' );
		if ( addPanel ) {
			addPanel.hidden = true;
		}

		var editor = editorOf( rowIdx );
		if ( editor ) {
			editor.hidden = false;
			var title = backdrop.querySelector( '#scfw-modal-title' );
			var label = editor.querySelector( '.scfw-field-label' );
			if ( title ) {
				title.textContent = label ? ( 'Edit: ' + label.value ) : 'Edit Field';
			}
		}

		backdrop.hidden = false;
	}

	function openModalForAdd() {
		if ( ! backdrop || ! modalBody ) {
			return;
		}

		modalBody.querySelectorAll( '.scfw-field-editor' ).forEach( function ( el ) {
			el.hidden = true;
		} );

		var addPanel = modalBody.querySelector( '.scfw-add-field' );
		if ( addPanel ) {
			addPanel.hidden = false;
			var title = backdrop.querySelector( '#scfw-modal-title' );
			if ( title ) {
				title.textContent = 'Add New Field';
			}
		}

		backdrop.hidden = false;
	}

	function closeModal() {
		if ( backdrop ) {
			backdrop.hidden = true;
		}
	}

	if ( topAddBtn ) {
		topAddBtn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			openModalForAdd();
		} );
	}

	if ( modalClose ) {
		modalClose.addEventListener( 'click', closeModal );
	}

	if ( backdrop ) {
		backdrop.addEventListener( 'click', function ( e ) {
			if ( e.target === backdrop || e.target.classList.contains( 'scfw-modal-cancel' ) ) {
				closeModal();
			}
		} );
	}

	// Click delegation inside table
	tbody.addEventListener( 'click', function ( event ) {
		var editBtn = event.target.closest( '.scfw-trigger-edit-field' );
		if ( editBtn ) {
			event.preventDefault();
			openModalForEditor( editBtn.dataset.row );
			return;
		}
	} );

	// Change delegation for table switches
	tbody.addEventListener( 'change', function ( event ) {
		var toggle = event.target.closest( '.scfw-toggle-input' );
		if ( toggle ) {
			var row = toggle.closest( '.scfw-field-row' );
			if ( row ) {
				row.classList.toggle( 'scfw-field-row--disabled', ! toggle.checked );
			}
		}
	} );

	// HTML5 Drag & Drop reordering on table rows
	tbody.addEventListener( 'dragstart', function ( event ) {
		var handle = event.target.closest( '.scfw-drag-handle' );
		if ( ! handle ) {
			return;
		}

		dragging = handle.closest( '.scfw-field-row' );
		if ( ! dragging ) {
			return;
		}

		dragging.classList.add( 'scfw-dragging' );
		event.dataTransfer.effectAllowed = 'move';
		event.dataTransfer.setData( 'text/plain', dragging.dataset.row );
	} );

	tbody.addEventListener( 'dragover', function ( event ) {
		var row = fieldRowOf( event.target );
		if ( ! row || row === dragging ) {
			return;
		}

		event.preventDefault();
		var rect       = row.getBoundingClientRect();
		var placeAfter = event.clientY - rect.top > rect.height / 2;

		tbody.insertBefore( dragging, placeAfter ? row.nextElementSibling : row );
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

	// Global keyboard accessibility: Close modal on Escape
	document.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key && backdrop && ! backdrop.hidden ) {
			closeModal();
		}
	} );

	// Confirm before destructive delete
	fieldsView.addEventListener( 'click', function ( event ) {
		var delBtn = event.target.closest( '.scfw-action-btn--delete, .scfw-remove' );
		if ( delBtn ) {
			var confirmMsg = delBtn.getAttribute( 'data-confirm' ) || 'Are you sure you want to delete this field?';
			if ( ! window.confirm( confirmMsg ) ) {
				event.preventDefault();
				return false;
			}
		}
	} );

	// Modal Editor Type Switching & Option Helpers
	if ( modalBody ) {
		modalBody.addEventListener( 'change', function ( event ) {
			if ( event.target.classList.contains( 'scfw-field-type' ) ) {
				var editor       = event.target.closest( '.scfw-field-editor' );
				var type         = event.target.value;
				var typeSettings = editor ? editor.querySelector( '.scfw-type-settings' ) : null;

				if ( editor ) {
					if ( typeSettings ) {
						typeSettings.hidden = ! hasDetails( type );
					}
					var optEditor = editor.querySelector( '.scfw-options-editor' );
					if ( optEditor ) {
						optEditor.hidden = ! isChoice( type );
					}
					var ph = editor.querySelector( '.scfw-placeholder-field' );
					if ( ph ) {
						ph.hidden = ! isText( type );
					}
					var def = editor.querySelector( '.scfw-default-field' );
					if ( def ) {
						def.hidden = ! ( isChoice( type ) || isText( type ) );
					}
					var desc = editor.querySelector( '.scfw-description-field' );
					if ( desc ) {
						desc.hidden = ! isTextarea( type );
					}
				}
			}
		} );

		modalBody.addEventListener( 'click', function ( event ) {
			var target = event.target;
			if ( target.classList.contains( 'scfw-option-add' ) ) {
				var optEditor = target.closest( '.scfw-options-editor' );
				var listEl    = optEditor ? optEditor.querySelector( '.scfw-options-list' ) : null;
				var template  = optEditor ? optEditor.querySelector( '.scfw-option-template' ) : null;

				if ( listEl && template ) {
					listEl.appendChild( template.content.firstElementChild.cloneNode( true ) );
				}
				return;
			}

			if ( target.classList.contains( 'scfw-option-remove' ) ) {
				var removeRow = target.closest( '.scfw-option-row' );
				if ( removeRow ) {
					removeRow.remove();
				}
				return;
			}

			if ( target.classList.contains( 'scfw-option-up' ) ) {
				var upRow = target.closest( '.scfw-option-row' );
				if ( upRow && upRow.previousElementSibling && upRow.previousElementSibling.classList.contains( 'scfw-option-row' ) ) {
					upRow.parentNode.insertBefore( upRow, upRow.previousElementSibling );
				}
				return;
			}

			if ( target.classList.contains( 'scfw-option-down' ) ) {
				var downRow = target.closest( '.scfw-option-row' );
				if ( downRow && downRow.nextElementSibling && downRow.nextElementSibling.classList.contains( 'scfw-option-row' ) ) {
					downRow.parentNode.insertBefore( downRow.nextElementSibling, downRow );
				}
			}
		} );
	}
}() );
