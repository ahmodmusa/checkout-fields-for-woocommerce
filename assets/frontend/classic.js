/**
 * SCFW Classic Checkout - Conditional Visibility Evaluator
 *
 * Deterministic client-side evaluator that only shows/hides conditional fields (.form-row.scfw-field)
 * whose conditions evaluate to false. Unconditional fields are never touched.
 */
(function() {
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

	function getFieldValue(fieldId) {
		if (!fieldId) {
			return null;
		}

		// 1. Radio buttons by name
		var radios = document.querySelectorAll('input[type="radio"][name="' + CSS.escape(fieldId) + '"]');
		if (radios.length > 0) {
			for (var i = 0; i < radios.length; i++) {
				if (radios[i].checked) {
					return radios[i].value;
				}
			}
			return '';
		}

		// 2. Checkbox by name or id
		var checkbox = document.querySelector('input[type="checkbox"][name="' + CSS.escape(fieldId) + '"]') ||
			document.getElementById(fieldId);
		if (checkbox && checkbox.type === 'checkbox') {
			return checkbox.checked ? (checkbox.value || 'yes') : 'no';
		}

		// 3. Select, textarea, text, number, date, tel, email input by name or id
		var el = document.querySelector('select[name="' + CSS.escape(fieldId) + '"], textarea[name="' + CSS.escape(fieldId) + '"], input[name="' + CSS.escape(fieldId) + '"]') ||
			document.getElementById(fieldId);

		if (el) {
			return el.value;
		}

		return null; // Controller does not exist in DOM
	}

	function evaluateConditionalFields() {
		// Target only elements with data-scfw-condition-field
		var conditionalRows = document.querySelectorAll('.form-row.scfw-field[data-scfw-condition-field], [data-scfw-condition-field]');
		if (!conditionalRows.length) {
			return;
		}

		// Deduplicate rows to evaluate each row once
		var processedRows = [];

		conditionalRows.forEach(function(el) {
			var row = el.classList.contains('form-row') ? el : el.closest('.form-row');
			if (!row || processedRows.indexOf(row) !== -1) {
				return;
			}
			processedRows.push(row);

			var controllingField = row.getAttribute('data-scfw-condition-field') || (el.getAttribute ? el.getAttribute('data-scfw-condition-field') : null);
			var operator = row.getAttribute('data-scfw-condition-operator') || (el.getAttribute ? el.getAttribute('data-scfw-condition-operator') : 'equals') || 'equals';
			var expectedValue = row.getAttribute('data-scfw-condition-value') || (el.getAttribute ? el.getAttribute('data-scfw-condition-value') : '') || '';

			if (!controllingField) {
				return; // Unconditional field -> do not touch
			}

			var actualRaw = getFieldValue(controllingField);

			// If the controller does not exist in the DOM, keep the field visible (safe fallback)
			if (actualRaw === null) {
				row.style.display = '';
				return;
			}

			var actual = normalizeValue(actualRaw);
			var expected = normalizeValue(expectedValue);

			var matches = (actual === expected);
			var isVisible = true;

			if (operator === 'equals') {
				isVisible = matches;
			} else if (operator === 'not_equals') {
				isVisible = !matches;
			}

			row.style.display = isVisible ? '' : 'none';
		});
	}

	function init() {
		evaluateConditionalFields();

		// Event listeners for user interaction
		document.addEventListener('change', evaluateConditionalFields);
		document.addEventListener('input', evaluateConditionalFields);

		// WooCommerce checkout update hooks
		if (window.jQuery) {
			window.jQuery(document.body).on('updated_checkout init_checkout', evaluateConditionalFields);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
