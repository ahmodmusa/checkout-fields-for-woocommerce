(function () {
	'use strict';

	function upgradeInput( input ) {
		if ( ! input || 'date' !== input.getAttribute( 'data-scfw-type' ) ) {
			return;
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

	function upgradeAll() {
		var inputs = document.querySelectorAll( 'input[data-scfw-type="date"]' );
		var i;

		for ( i = 0; i < inputs.length; i++ ) {
			upgradeInput( inputs[ i ] );
		}
	}

	function init() {
		upgradeAll();

		if ( 'undefined' === typeof window.MutationObserver ) {
			return;
		}

		new window.MutationObserver( upgradeAll ).observe(
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