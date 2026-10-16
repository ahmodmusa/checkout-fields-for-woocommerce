/**
 * SCFW Block Checkout - Native Date Enhancement & Conditional Visibility Bridge
 *
 * 1. Upgrades marked Block date fields into native calendar pickers.
 * 2. Uses wp.data checkoutStore subscriptions (with fallback DOM evaluation)
 *    to evaluate SCFW conditional rules for registered additional checkout fields.
 */
(function () {
	'use strict';

	function normalizeValue(val) {
		if (val === true || val === 1 || val === '1' || val === 'yes' || val === 'on') {
			return 'yes';
		}
		if (val === false || val === 0 || val === '0' || val === 'no' || val === 'off' || val === null || val === undefined) {
			return 'no';
		}
		return String(val).trim().toLowerCase();
	}

	function upgradeInput( input ) {
		if ( ! input || 'date' !== input.getAttribute( 'data-scfw-type' ) ) {
			return;
		}

		var wrapper = input.closest( '.wc-block-components-text-input' );

		if ( wrapper ) {
			wrapper.classList.add( 'scfw-date-wrapper' );
		}

		if ( 'date' === input.type ) {
			return;
		}

		var value = input.value;

		input.type = 'date';

		if ( value && input.value !== value ) {
			input.value = value;
		}
	}

	function getFieldValue(fieldId) {
		if (!fieldId) {
			return null;
		}

		// 1. Try reading from WooCommerce Blocks Data Store (wp.data)
		if (window.wp && window.wp.data && window.wc && window.wc.wcBlocksData) {
			try {
				var checkoutStore = window.wp.data.select(window.wc.wcBlocksData.checkoutStore);
				if (checkoutStore && typeof checkoutStore.getAdditionalFields === 'function') {
					var additionalFields = checkoutStore.getAdditionalFields() || {};
					var namespacedId = 'scfw/' + fieldId;
					if (typeof additionalFields[namespacedId] !== 'undefined') {
						return additionalFields[namespacedId];
					}
					if (typeof additionalFields[fieldId] !== 'undefined') {
						return additionalFields[fieldId];
					}
				}
			} catch (e) {
				// Fallback to DOM below
			}
		}

		// 2. Fallback: DOM query by name or id including WooCommerce Block patterns
		var candidates = [
			fieldId,
			'scfw/' + fieldId,
			'scfw_' + fieldId,
			'scfw-' + fieldId,
			'order_scfw/' + fieldId,
			'order-scfw-' + fieldId,
			'address_scfw/' + fieldId,
			'address-scfw-' + fieldId,
			'contact_scfw/' + fieldId,
			'contact-scfw-' + fieldId
		];

		for (var c = 0; c < candidates.length; c++) {
			var name = candidates[c];

			// Checkbox
			var checkbox = document.querySelector('input[type="checkbox"][name="' + CSS.escape(name) + '"]') ||
				document.getElementById(name);
			if (checkbox && checkbox.type === 'checkbox') {
				return checkbox.checked ? (checkbox.value || 'yes') : 'no';
			}

			// Radio
			var radios = document.querySelectorAll('input[type="radio"][name="' + CSS.escape(name) + '"]');
			if (radios.length > 0) {
				for (var i = 0; i < radios.length; i++) {
					if (radios[i].checked) {
						return radios[i].value;
					}
				}
			}

			// Select / Input / Textarea
			var el = document.querySelector('select[name="' + CSS.escape(name) + '"], textarea[name="' + CSS.escape(name) + '"], input[name="' + CSS.escape(name) + '"]') ||
				document.getElementById(name);
			if (el) {
				return el.value;
			}
		}

		// Broad class search for Block container
		var blockClassContainer = document.querySelector('.wc-block-components-address-form__scfw-' + CSS.escape(fieldId) + ', .wc-block-components-select-input-scfw-' + CSS.escape(fieldId));
		if (blockClassContainer) {
			var inputEl = blockClassContainer.querySelector('input, select, textarea');
			if (inputEl) {
				if (inputEl.type === 'checkbox') {
					return inputEl.checked ? (inputEl.value || 'yes') : 'no';
				}
				return inputEl.value;
			}
		}

		return null;
	}

	function findFieldWrapper(fieldId) {
		var candidates = [
			fieldId,
			'scfw/' + fieldId,
			'scfw_' + fieldId,
			'scfw-' + fieldId,
			'order_scfw/' + fieldId,
			'order-scfw-' + fieldId,
			'address_scfw/' + fieldId,
			'address-scfw-' + fieldId,
			'contact_scfw/' + fieldId,
			'contact-scfw-' + fieldId
		];

		// 1. Check direct class container rendered by WooCommerce Blocks
		var directClassContainer = document.querySelector('.wc-block-components-address-form__scfw-' + CSS.escape(fieldId) + ', .wc-block-components-select-input-scfw-' + CSS.escape(fieldId));
		if (directClassContainer) {
			return directClassContainer;
		}

		// 2. Search by input candidate names and ids
		for (var c = 0; c < candidates.length; c++) {
			var name = candidates[c];
			var el = document.querySelector('[name="' + CSS.escape(name) + '"]') ||
				document.getElementById(name) ||
				document.querySelector('[data-field-id*="' + CSS.escape(fieldId) + '"]');

			if (el) {
				var wrapper = el.closest('.wc-block-components-text-input') ||
					el.closest('.wc-block-components-select') ||
					el.closest('.wc-block-components-select-input') ||
					el.closest('.wc-block-components-checkbox') ||
					el.closest('.wc-block-components-radio-control') ||
					el.closest('.wc-block-components-form-field') ||
					el.parentElement;

				if (wrapper) {
					return wrapper;
				}
			}
		}

		return null;
	}

	function evaluateBlockConditions() {
		if (typeof window.scfwBlockConditions === 'undefined' || !window.scfwBlockConditions) {
			return;
		}

		var conditions = window.scfwBlockConditions;

		Object.keys(conditions).forEach(function(dependentId) {
			var rule = conditions[dependentId];
			if (!rule || !rule.field) {
				return;
			}

			var wrapper = findFieldWrapper(dependentId);
			if (!wrapper) {
				return;
			}

			var actualRaw = getFieldValue(rule.field);

			// Safe fallback if controller is not present in state or DOM
			if (actualRaw === null) {
				wrapper.style.display = '';
				return;
			}

			var actual = normalizeValue(actualRaw);
			var expected = normalizeValue(rule.value || '');
			var operator = rule.operator || 'equals';

			var matches = (actual === expected);
			var isVisible = true;

			if (operator === 'equals') {
				isVisible = matches;
			} else if (operator === 'not_equals') {
				isVisible = !matches;
			}

			wrapper.style.display = isVisible ? '' : 'none';
		});
	}

	function runAll() {
		var dateInputs = document.querySelectorAll( 'input[data-scfw-type="date"]' );
		for ( var i = 0; i < dateInputs.length; i++ ) {
			upgradeInput( dateInputs[ i ] );
		}

		evaluateBlockConditions();
	}

	function init() {
		runAll();

		// Subscribe to wp.data store changes
		if (window.wp && window.wp.data && window.wc && window.wc.wcBlocksData) {
			try {
				window.wp.data.subscribe(function() {
					evaluateBlockConditions();
				});
			} catch (e) {
				// No-op
			}
		}

		// DOM event listeners
		document.addEventListener('change', runAll);
		document.addEventListener('input', runAll);

		if ( 'undefined' === typeof window.MutationObserver ) {
			return;
		}

		new window.MutationObserver( runAll ).observe(
			document.body,
			{ childList: true, subtree: true }
		);
	}

	if ( document.body ) {
		init();
	} else {
		document.addEventListener( 'DOMContentLoaded', init );
	}
})();