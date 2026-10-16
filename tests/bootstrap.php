<?php
/**
 * Minimal test bootstrap for the Free field configuration layer.
 *
 * The classes under test depend on a handful of WordPress sanitization and
 * options functions. The stubs below mirror the WordPress semantics that the
 * schema layer relies on; they exist only to run the unit tests outside a
 * WordPress installation.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

/**
 * Stub storage for get_option/update_option.
 *
 * @var array<string, mixed> $GLOBALS
 */
$GLOBALS['scfw_test_options'] = array();

/**
 * Get the stub option value.
 *
 * @param string $option  Option name.
 * @param mixed  $default Default fallback.
 * @return mixed Stub value.
 */
function get_option( string $option, mixed $default = false ): mixed {
	if ( is_array( $GLOBALS['scfw_test_options'] ) && array_key_exists( $option, $GLOBALS['scfw_test_options'] ) ) {
		return $GLOBALS['scfw_test_options'][ $option ];
	}

	return $default;
}

/**
 * Translate a string the way __() does.
 *
 * @param string $text   Text to translate.
 * @param string $domain Text domain.
 * @return string Untranslated text.
 */
function __( string $text, string $domain = 'default' ): string {
	return $text;
}

/**
 * Escape html the way esc_html() does.
 *
 * @param string $text Raw text.
 * @return string Escaped html text.
 */
function esc_html( string $text ): string {
	return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

/**
 * Display escaped html.
 *
 * @param string $text Raw text.
 * @return void
 */
function esc_html_e( string $text, string $domain = 'default' ): void {
	echo htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

/**
 * Escape attribute the way esc_attr() does.
 *
 * @param string $text Raw text.
 * @return string Escaped attribute text.
 */
function esc_attr( string $text ): string {
	return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

/**
 * Display escaped attribute.
 *
 * @param string $text Raw text.
 * @return void
 */
function esc_attr_e( string $text, string $domain = 'default' ): void {
	echo htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

/**
 * Escape textarea text.
 *
 * @param string $text Raw text.
 * @return string Escaped textarea string.
 */
function esc_textarea( string $text ): string {
	return htmlspecialchars( $text, ENT_QUOTES, 'UTF-8' );
}

/**
 * Output checked attribute if values match.
 *
 * @param mixed $checked One value.
 * @param mixed $current Other value.
 * @param bool  $echo    Whether to echo.
 * @return string
 */
function checked( mixed $checked, mixed $current = true, bool $echo = true ): string {
	$result = ( (string) $checked === (string) $current ) ? ' checked="checked"' : '';
	if ( $echo ) {
		echo $result;
	}
	return $result;
}

/**
 * Output selected attribute if values match.
 *
 * @param mixed $selected One value.
 * @param mixed $current  Other value.
 * @param bool  $echo     Whether to echo.
 * @return string
 */
function selected( mixed $selected, mixed $current = true, bool $echo = true ): string {
	$result = ( (string) $selected === (string) $current ) ? ' selected="selected"' : '';
	if ( $echo ) {
		echo $result;
	}
	return $result;
}

/**
 * Sanitize a key the way sanitize_key() does.
 *
 * @param string $key Raw key.
 * @return string Lowercase alphanumeric, dash and underscore only.
 */
function sanitize_key( string $key ): string {
	return strtolower( (string) preg_replace( '/[^a-z0-9_-]/', '', $key ) ?? '' );
}

/**
 * Sanitize a title the way sanitize_title() does.
 *
 * @param string $title Raw title.
 * @return string Lowercased slug.
 */
function sanitize_title( string $title ): string {
	return strtolower( (string) preg_replace( '/[^a-zA-Z0-9_-]/', '-', $title ) ?? '' );
}

/**
 * Sanitize a text field the way sanitize_text_field() does.
 *
 * @param string $text Raw text.
 * @return string Trimmed single-line text.
 */
function sanitize_text_field( string $text ): string {
	$text = (string) preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );
	$text = (string) preg_replace( '/<[^>]*>/', '', $text );
	$text = str_replace( '&#038;', '&', $text );
	$text = str_replace( array( "\r", "\n", "\t" ), ' ', $text );
	$text = (string) preg_replace( '/[ \t]+/', ' ', $text );
	return trim( $text );
}

/**
 * Sanitize a multiline textarea field the way sanitize_textarea_field() does.
 *
 * @param string $text Raw text.
 * @return string Multiline text with HTML stripped.
 */
function sanitize_textarea_field( string $text ): string {
	$text = (string) preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $text );
	$text = (string) preg_replace( '/<[^>]*>/', '', $text );
	$text = str_replace( '&#038;', '&', $text );
	return trim( $text );
}

/**
 * Absolutize a number the way absint() does.
 *
 * @param mixed $number Raw value.
 * @return int Absolute integer.
 */
function absint( mixed $number ): int {
	return abs( intval( $number ) );
}

/**
 * No-op settings error collector.
 *
 * @param string $setting Setting id.
 * @param string $code    Error code.
 * @param string $message Error message.
 * @param string $type    Error type.
 * @return void
 */
function add_settings_error( string $setting, string $code, string $message, string $type = 'error' ): void {}

/**
 * Minimal WP_Error stub for unit testing.
 */
if ( ! class_exists( 'WP_Error' ) ) {
	class WP_Error {
		private array $errors = array();

		public function add( string|int $code, string $message, mixed $data = '' ): void {
			$this->errors[ (string) $code ][] = $message;
		}

		public function get_error_codes(): array {
			return array_keys( $this->errors );
		}

		public function get_error_message( string|int $code = '' ): string {
			if ( empty( $code ) ) {
				$codes = $this->get_error_codes();
				$code  = reset( $codes );
			}
			return $this->errors[ (string) $code ][0] ?? '';
		}
	}
}

/**
 * Passthrough apply_filters stub.
 *
 * @param string $hook_name Hook name.
 * @param mixed  $value     Initial value.
 * @param mixed  ...$args   Additional arguments.
 * @return mixed Filtered value.
 */
function apply_filters( string $hook_name, mixed $value, mixed ...$args ): mixed {
	return $value;
}

/**
 * Minimal woocommerce_form_field stub for unit testing.
 */
function woocommerce_form_field( string $key, array $args = array(), mixed $value = null ): string {
	$class = isset( $args['class'] ) && is_array( $args['class'] ) ? implode( ' ', $args['class'] ) : '';
	return sprintf( '<p class="form-row %s" id="%s_field"><input type="text" name="%s" value="%s" /></p>', $class, $key, $key, (string) $value );
}

/**
 * Stub for current_user_can.
 */
$GLOBALS['scfw_test_current_user_can'] = true;
function current_user_can( string $capability, mixed ...$args ): bool {
	return (bool) ( $GLOBALS['scfw_test_current_user_can'] ?? true );
}

/**
 * Stub for wp_verify_nonce.
 */
$GLOBALS['scfw_test_valid_nonce'] = true;
function wp_verify_nonce( string $nonce, string|int $action = -1 ): int|bool {
	return ! empty( $GLOBALS['scfw_test_valid_nonce'] ) ? 1 : false;
}

/**
 * Stub for wp_nonce_field.
 */
function wp_nonce_field( string|int $action = -1, string $name = '_wpnonce', bool $referer = true, bool $echo = true ): string {
	$html = sprintf( '<input type="hidden" name="%s" value="test_nonce" />', esc_attr( $name ) );
	if ( $echo ) {
		echo $html;
	}
	return $html;
}

/**
 * Stub for wp_unslash.
 */
function wp_unslash( mixed $value ): mixed {
	return $value;
}

/**
 * Stub for sanitize_email.
 */
function sanitize_email( string $email ): string {
	return filter_var( $email, FILTER_VALIDATE_EMAIL ) ? $email : '';
}