/**
 * Checkout field builder interactions.
 *
 * Supports the reference-accurate table layout, modal/drawer editor, inline
 * enabled switches, and HTML5 drag-and-drop row reordering.
 */
( function () {
	'use strict';

	var fieldsView         = document.getElementById( 'scfw-fields-view' );
	var tbody              = document.getElementById( 'scfw-fields-tbody' );
	var backdrop           = document.getElementById( 'scfw-editor-backdrop' );
	var modalClose         = document.getElementById( 'scfw-modal-close' );
	var modalBody          = document.getElementById( 'scfw-modal-body' );
	var topAddBtn          = document.getElementById( 'scfw-top-add-btn' );
	var topTemplatesBtn    = document.getElementById( 'scfw-top-templates-btn' );
	var templatesBackdrop  = document.getElementById( 'scfw-templates-backdrop' );
	var templatesClose     = document.getElementById( 'scfw-templates-modal-close' );
	var healthBtn          = document.getElementById( 'scfw-health-btn' );
	var healthBackdrop     = document.getElementById( 'scfw-health-backdrop' );
	var healthClose        = document.getElementById( 'scfw-health-modal-close' );

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

	function openTemplatesModal() {
		if ( templatesBackdrop ) {
			templatesBackdrop.hidden = false;
		}
	}

	function closeTemplatesModal() {
		if ( templatesBackdrop ) {
			templatesBackdrop.hidden = true;
		}
	}

	function openHealthModal() {
		if ( healthBackdrop ) {
			healthBackdrop.hidden = false;
		}
	}

	function closeHealthModal() {
		if ( healthBackdrop ) {
			healthBackdrop.hidden = true;
		}
	}

	if ( topAddBtn ) {
		topAddBtn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			openModalForAdd();
		} );
	}

	if ( topTemplatesBtn ) {
		topTemplatesBtn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			openTemplatesModal();
		} );
	}

	if ( healthBtn ) {
		healthBtn.addEventListener( 'click', function ( e ) {
			e.preventDefault();
			openHealthModal();
		} );
	}

	if ( modalClose ) {
		modalClose.addEventListener( 'click', closeModal );
	}

	if ( templatesClose ) {
		templatesClose.addEventListener( 'click', closeTemplatesModal );
	}

	if ( healthClose ) {
		healthClose.addEventListener( 'click', closeHealthModal );
	}

	if ( backdrop ) {
		backdrop.addEventListener( 'click', function ( e ) {
			if ( e.target === backdrop || e.target.classList.contains( 'scfw-modal-cancel' ) ) {
				closeModal();
			}
		} );
	}

	if ( templatesBackdrop ) {
		templatesBackdrop.addEventListener( 'click', function ( e ) {
			if ( e.target === templatesBackdrop || e.target.classList.contains( 'scfw-templates-modal-cancel' ) ) {
				closeTemplatesModal();
			}
		} );
	}

	if ( healthBackdrop ) {
		healthBackdrop.addEventListener( 'click', function ( e ) {
			if ( e.target === healthBackdrop || e.target.classList.contains( 'scfw-health-modal-cancel' ) ) {
				closeHealthModal();
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
		if ( 'Escape' === event.key ) {
			if ( backdrop && ! backdrop.hidden ) {
				closeModal();
			}
			if ( templatesBackdrop && ! templatesBackdrop.hidden ) {
				closeTemplatesModal();
			}
			if ( healthBackdrop && ! healthBackdrop.hidden ) {
				closeHealthModal();
			}
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

		// Live Field Preview Engine
		function escapeHtml( str ) {
			if ( ! str ) return '';
			return String( str )
				.replace( /&/g, '&amp;' )
				.replace( /</g, '&lt;' )
				.replace( />/g, '&gt;' )
				.replace( /"/g, '&quot;' )
				.replace( /'/g, '&#039;' );
		}

		function updateFieldPreview( editor ) {
			if ( ! editor ) return;
			var previewBox = editor.querySelector( '.scfw-live-preview-box' );
			if ( ! previewBox ) return;

			var labelInput       = editor.querySelector( '.scfw-field-label' );
			var idInput          = editor.querySelector( '.scfw-field-id' );
			var typeSelect       = editor.querySelector( '.scfw-field-type' );
			var reqCheckbox      = editor.querySelector( '.scfw-required-control input[type="checkbox"]' );
			var enabledCheckbox  = editor.querySelector( '.scfw-status-control input[type="checkbox"]' );
			var placeholderInput = editor.querySelector( '.scfw-placeholder-field input[type="text"]' );
			var defaultInput     = editor.querySelector( '.scfw-default-field input[type="text"]' );
			var descInput        = editor.querySelector( '.scfw-description-field input[type="text"]' );

			var label       = ( labelInput && labelInput.value ) ? labelInput.value : 'Untitled Field';
			var fieldId     = ( idInput && idInput.value ) ? idInput.value : 'field';
			var type        = ( typeSelect && typeSelect.value ) ? typeSelect.value : 'text';
			var isRequired  = reqCheckbox ? reqCheckbox.checked : false;
			var isEnabled   = enabledCheckbox ? enabledCheckbox.checked : true;
			var placeholder = ( placeholderInput && placeholderInput.value ) ? placeholderInput.value : '';
			var defaultVal  = ( defaultInput && defaultInput.value ) ? defaultInput.value : '';
			var description = ( descInput && descInput.value ) ? descInput.value : '';

			// Read options if choice type
			var options = [];
			var optionInputs = editor.querySelectorAll( '.scfw-options-list .scfw-option-value' );
			optionInputs.forEach( function ( optInp ) {
				var val = optInp.value.trim();
				if ( val ) {
					options.push( val );
				}
			} );

			// Check conditions
			var condFieldSelect = editor.querySelector( '.scfw-cond-field-select' );
			var condOpSelect    = editor.querySelector( '.scfw-cond-op-select' );
			var condValInput    = editor.querySelector( '.scfw-cond-val-input' );
			var hasCondition    = condFieldSelect && condFieldSelect.value !== '';
			var conditionText   = '';
			if ( hasCondition ) {
				var cField = condFieldSelect.value;
				var cOp    = condOpSelect ? condOpSelect.value : 'equals';
				var cVal   = condValInput ? condValInput.value : '';
				conditionText = 'Conditional: ' + cField + ' ' + cOp + ( cVal ? ' ' + cVal : '' );
			}

			// Update container state
			if ( isEnabled ) {
				previewBox.classList.remove( 'scfw-preview--disabled' );
			} else {
				previewBox.classList.add( 'scfw-preview--disabled' );
			}

			var previewId = 'scfw_preview_input_' + fieldId.replace( /[^a-z0-9_-]/gi, '_' );

			// Build preview HTML
			var html = '';
			html += '<div class="scfw-preview-header">';
			html += '<div class="scfw-preview-meta-left">';
			html += '<span class="scfw-preview-badge">Customer Checkout Preview</span>';
			if ( ! isEnabled ) {
				html += '<span class="scfw-preview-status-badge scfw-preview-status--disabled">Field Disabled</span>';
			}
			html += '</div>';

			if ( hasCondition ) {
				html += '<div class="scfw-preview-condition-tag" title="' + escapeHtml( conditionText ) + '">';
				html += '<span class="dashicons dashicons-randomize" aria-hidden="true" style="font-size:13px; width:13px; height:13px; line-height:1.4;"></span> ';
				html += '<span>' + escapeHtml( conditionText ) + '</span>';
				html += '</div>';
			}
			html += '</div>';

			html += '<div class="scfw-preview-surface">';
			html += '<div class="scfw-preview-form-row form-row form-row-wide scfw-preview-type--' + escapeHtml( type ) + '">';

			var reqBadge = isRequired ? ' <abbr class="required" title="required" style="color:#e11d48; margin-left:4px;">*</abbr>' : '';

			if ( 'checkbox' === type ) {
				var checkedAttr = defaultVal ? ' checked' : '';
				html += '<label class="checkbox scfw-preview-checkbox-label">';
				html += '<input type="checkbox" id="' + escapeHtml( previewId ) + '" class="input-checkbox" disabled' + checkedAttr + ' />';
				html += '<span class="scfw-preview-label-text">' + escapeHtml( label ) + '</span>' + reqBadge;
				html += '</label>';
			} else {
				html += '<label for="' + escapeHtml( previewId ) + '" class="scfw-preview-label">';
				html += '<span class="scfw-preview-label-text">' + escapeHtml( label ) + '</span>' + reqBadge;
				html += '</label>';

				if ( 'textarea' === type ) {
					html += '<textarea id="' + escapeHtml( previewId ) + '" class="input-text scfw-preview-textarea" placeholder="' + escapeHtml( placeholder ) + '" readonly rows="3">' + escapeHtml( defaultVal ) + '</textarea>';
				} else if ( 'select' === type ) {
					html += '<select id="' + escapeHtml( previewId ) + '" class="select scfw-preview-select" disabled>';
					if ( placeholder ) {
						html += '<option value="">' + escapeHtml( placeholder ) + '</option>';
					}
					if ( options.length === 0 ) {
						html += '<option value="">No options configured yet.</option>';
					} else {
						for ( var i = 0; i < options.length; i++ ) {
							var sel = ( defaultVal === options[ i ] ) ? ' selected' : '';
							html += '<option value="' + escapeHtml( options[ i ] ) + '"' + sel + '>' + escapeHtml( options[ i ] ) + '</option>';
						}
					}
					html += '</select>';
				} else if ( 'radio' === type ) {
					html += '<div class="scfw-preview-radio-group">';
					if ( options.length === 0 ) {
						html += '<p class="scfw-preview-empty-notice">No options configured yet.</p>';
					} else {
						for ( var j = 0; j < options.length; j++ ) {
							var radChecked = ( defaultVal === options[ j ] ) ? ' checked' : '';
							html += '<label class="scfw-preview-radio-option">';
							html += '<input type="radio" name="' + escapeHtml( previewId ) + '" value="' + escapeHtml( options[ j ] ) + '" disabled' + radChecked + ' /> ';
							html += '<span>' + escapeHtml( options[ j ] ) + '</span>';
							html += '</label>';
						}
					}
					html += '</div>';
				} else if ( 'number' === type ) {
					html += '<input type="number" id="' + escapeHtml( previewId ) + '" class="input-text" placeholder="' + escapeHtml( placeholder ) + '" value="' + escapeHtml( defaultVal ) + '" readonly />';
				} else if ( 'date' === type ) {
					html += '<input type="date" id="' + escapeHtml( previewId ) + '" class="input-text" value="' + escapeHtml( defaultVal ) + '" readonly />';
				} else if ( 'email' === type ) {
					html += '<input type="email" id="' + escapeHtml( previewId ) + '" class="input-text" placeholder="' + escapeHtml( placeholder ) + '" value="' + escapeHtml( defaultVal ) + '" readonly />';
				} else if ( 'tel' === type ) {
					html += '<input type="tel" id="' + escapeHtml( previewId ) + '" class="input-text" placeholder="' + escapeHtml( placeholder ) + '" value="' + escapeHtml( defaultVal ) + '" readonly />';
				} else {
					html += '<input type="text" id="' + escapeHtml( previewId ) + '" class="input-text" placeholder="' + escapeHtml( placeholder ) + '" value="' + escapeHtml( defaultVal ) + '" readonly />';
				}

				if ( description && 'textarea' === type ) {
					html += '<span class="description scfw-preview-description">' + escapeHtml( description ) + '</span>';
				}
			}

			html += '</div>';
			html += '</div>';

			previewBox.innerHTML = html;
		}

		// Listen to all inputs inside modal body to update preview live
		modalBody.addEventListener( 'input', function ( event ) {
			var editor = event.target.closest( '.scfw-field-editor' );
			if ( editor ) {
				updateFieldPreview( editor );
			}
		} );

		modalBody.addEventListener( 'change', function ( event ) {
			var editor = event.target.closest( '.scfw-field-editor' );
			if ( editor ) {
				updateFieldPreview( editor );
			}
		} );

		// Hook into option add/remove/reorder
		modalBody.addEventListener( 'click', function ( event ) {
			var target = event.target;
			if (
				target.classList.contains( 'scfw-option-add' ) ||
				target.classList.contains( 'scfw-option-remove' ) ||
				target.classList.contains( 'scfw-option-up' ) ||
				target.classList.contains( 'scfw-option-down' )
			) {
				setTimeout( function () {
					var editor = target.closest( '.scfw-field-editor' );
					if ( editor ) {
						updateFieldPreview( editor );
					}
				}, 50 );
			}
		} );

		// Copy meta key handler with clipboard API and fallback.
		document.addEventListener( 'click', function ( event ) {
			var btn = event.target.closest( '.scfw-copy-key-btn' );
			if ( ! btn ) {
				return;
			}

			event.preventDefault();
			var key = btn.getAttribute( 'data-key' ) || '';
			if ( ! key ) {
				return;
			}

			var originalText = btn.textContent;

			function showCopied() {
				btn.textContent = 'Copied!';
				btn.classList.add( 'scfw-copied' );
				setTimeout( function () {
					btn.textContent = originalText;
					btn.classList.remove( 'scfw-copied' );
				}, 1800 );
			}

			if ( navigator.clipboard && navigator.clipboard.writeText ) {
				navigator.clipboard.writeText( key ).then( function () {
					showCopied();
				} ).catch( function () {
					fallbackCopy( key );
				} );
			} else {
				fallbackCopy( key );
			}

			function fallbackCopy( text ) {
				var tempInput = document.createElement( 'input' );
				tempInput.setAttribute( 'value', text );
				tempInput.style.position = 'fixed';
				tempInput.style.opacity = '0';
				document.body.appendChild( tempInput );
				tempInput.select();
				try {
					document.execCommand( 'copy' );
					showCopied();
				} catch ( err ) {
					// Silent fail or standard notification
				}
				document.body.removeChild( tempInput );
			}
		} );
	}
}() );
