<?php
/**
 * Field configuration import and export.
 *
 * @package SCFW\Admin
 */

declare(strict_types=1);

namespace SCFW\Admin;

use SCFW\Core\Field\FieldRepository;

/**
 * Handles the import and export of the checkout field configuration.
 *
 * Both actions require the manage_options capability and a WordPress nonce.
 * Imported JSON is structurally validated and sanitized with the same Core
 * schema rules used by the settings form, and never executes arbitrary code.
 * The exported configuration is deterministic and importable again.
 *
 * @since 0.0.1
 */
final class FieldTransfer {

	/**
	 * Settings option name.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'scfw_options';

	/**
	 * Stable format marker for exported configuration files.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const FORMAT = 'checkout-fields-config-v1';

	/**
	 * Admin settings page location.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const PAGE_URL = 'admin.php?page=scfw';

	/**
	 * Render the import and export controls.
	 *
	 * The controls intentionally live outside the WordPress settings form
	 * because they need a separate multipart POST and are not part of the
	 * options flow.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function render(): void {
		?>
		<hr />
		<h2><?php esc_html_e( 'Import / Export', 'checkout-fields-for-woocommerce' ); ?></h2>

		<h3><?php esc_html_e( 'Export Configuration', 'checkout-fields-for-woocommerce' ); ?></h3>
		<p>
			<?php esc_html_e( 'Download the current checkout field configuration as a JSON file that can be imported on another installation.', 'checkout-fields-for-woocommerce' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'scfw_export' ); ?>
			<input type="hidden" name="action" value="scfw_export" />
			<?php submit_button( __( 'Export Configuration', 'checkout-fields-for-woocommerce' ), 'secondary', 'scfw_export', false ); ?>
		</form>

		<h3><?php esc_html_e( 'Import Configuration', 'checkout-fields-for-woocommerce' ); ?></h3>
		<p>
			<?php esc_html_e( 'Import a previously exported configuration file. The current field configuration is replaced by the imported one.', 'checkout-fields-for-woocommerce' ); ?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
			<?php wp_nonce_field( 'scfw_import' ); ?>
			<input type="hidden" name="action" value="scfw_import" />
			<input type="file" name="scfw_import_file" accept="application/json" required />
			<?php submit_button( __( 'Import Configuration', 'checkout-fields-for-woocommerce' ), 'secondary', 'scfw_import', false ); ?>
		</form>
		<?php
	}

	/**
	 * Export the effective field configuration as a JSON download.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function export(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to export the configuration.', 'checkout-fields-for-woocommerce' ) );
		}

		check_admin_referer( 'scfw_export' );

		$payload = array(
			'format'  => self::FORMAT,
			'enabled' => self::isEnabled(),
			'fields'  => FieldRepository::config(),
		);

		$json = wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

		if ( false === $json ) {
			self::redirect( 'export_failed', __( 'The configuration could not be encoded.', 'checkout-fields-for-woocommerce' ), 'error' );
		}

		nocache_headers();

		$filename = sprintf( 'checkout-fields-config-%s.json', gmdate( 'Ymd-His' ) );

		header( 'Content-Type: application/json; charset=' . get_option( 'blog_charset', 'UTF-8' ) );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Content-Length: ' . strlen( $json ) );

		echo $json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON download content.
		exit;
	}

	/**
	 * Import a submitted configuration file.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function import(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to import the configuration.', 'checkout-fields-for-woocommerce' ) );
		}

		check_admin_referer( 'scfw_import' );

		$file = $_FILES['scfw_import_file'] ?? array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( ! is_array( $file ) || ( $file['error'] ?? UPLOAD_ERR_NO_FILE ) !== UPLOAD_ERR_OK ) {
			self::redirect( 'import_failed', __( 'The configuration file upload failed.', 'checkout-fields-for-woocommerce' ), 'error' );
		}

		$tmp_name = isset( $file['tmp_name'] ) && is_string( $file['tmp_name'] ) ? $file['tmp_name'] : '';
		$filename = isset( $file['name'] ) && is_scalar( $file['name'] ) ? sanitize_file_name( (string) $file['name'] ) : '';

		if ( '' === $tmp_name || ! is_uploaded_file( $tmp_name ) || ! file_exists( $tmp_name ) || ! is_readable( $tmp_name ) ) {
			self::redirect( 'import_failed', __( 'The uploaded configuration file could not be read.', 'checkout-fields-for-woocommerce' ), 'error' );
		}

		$file_type = wp_check_filetype( $filename, array( 'json' => 'application/json' ) );

		if ( ! is_array( $file_type ) || 'json' !== ( $file_type['ext'] ?? '' ) ) {
			self::redirect( 'import_failed', __( 'Only valid JSON configuration files can be imported.', 'checkout-fields-for-woocommerce' ), 'error' );
		}

		$content = file_get_contents( $tmp_name ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$data    = json_decode( (string) $content, true );

		if ( false === $content || JSON_ERROR_NONE !== json_last_error() || ! is_array( $data ) || ( $data['format'] ?? '' ) !== self::FORMAT ) {
			self::redirect( 'import_failed', __( 'The uploaded file is not a valid configuration export.', 'checkout-fields-for-woocommerce' ), 'error' );
		}

		if ( array_diff( array( 'format', 'enabled', 'fields' ), array_keys( $data ) ) || ! self::isBooleanValue( $data['enabled'] ) ) {
			self::redirect( 'import_failed', __( 'The uploaded configuration has an invalid structure.', 'checkout-fields-for-woocommerce' ), 'error' );
		}

		$imported = $data['fields'] ?? null;

		if ( ! is_array( $imported ) ) {
			self::redirect( 'import_failed', __( 'The uploaded configuration does not contain any valid fields.', 'checkout-fields-for-woocommerce' ), 'error' );
		}

		$fields = self::validatedFields( $imported );

		if ( null === $fields ) {
			self::redirect( 'import_failed', __( 'The uploaded configuration contains invalid fields and was rejected.', 'checkout-fields-for-woocommerce' ), 'error' );
		}

		$options = get_option( self::OPTION_NAME, array() );
		$options = is_array( $options ) ? $options : array();

		$options['enabled'] = isset( $data['enabled'] ) ? ( empty( $data['enabled'] ) ? 0 : 1 ) : ( isset( $options['enabled'] ) ? absint( $options['enabled'] ) : 0 );
		$options['fields']  = $fields;

		update_option( self::OPTION_NAME, $options );

		self::redirect( 'import_success', __( 'The field configuration was imported.', 'checkout-fields-for-woocommerce' ), 'success' );
	}

	/**
	 * Validate and sanitize the imported field entries.
	 *
	 * Unlike the settings form, an import is strict: any malformed entry
	 * rejects the whole file so a partial configuration is never persisted.
	 *
	 * @since 0.0.1
	 *
	 * @param array<array-key, mixed> $fields Imported field configuration.
	 *
	 * @return array<string, array{id: string, label: string, type: string, required: bool, enabled: bool, rules: array<int|string, mixed>}>|null Canonical field configuration, or null when invalid.
	 */
	private static function validatedFields( array $fields ): ?array {
		$clean = array();

		foreach ( $fields as $entry ) {
			if ( ! is_array( $entry ) ) {
				return null;
			}

			$normalized = FieldRepository::normalizeFieldEntry( $entry, true );

			if ( null === $normalized || isset( $clean[ $normalized['id'] ] ) ) {
				return null;
			}

			$clean[ $normalized['id'] ] = $normalized;
		}

		return $clean;
	}

	/**
	 * Check the scalar boolean representation used by the export format.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Value to check.
	 * @return bool True when the value is an accepted boolean representation.
	 */
	private static function isBooleanValue( mixed $value ): bool {
		return is_bool( $value ) || 0 === $value || 1 === $value || '0' === $value || '1' === $value;
	}

	/**
	 * Get whether the plugin is currently enabled.
	 *
	 * @since 0.0.1
	 *
	 * @return int 1 when enabled, 0 otherwise.
	 */
	private static function isEnabled(): int {
		$options = get_option( self::OPTION_NAME, array() );

		return is_array( $options ) && ! empty( $options['enabled'] ) ? 1 : 0;
	}

	/**
	 * Redirect back to the settings page with a notice.
	 *
	 * @since 0.0.1
	 *
	 * @param string $code    Error code.
	 * @param string $message Notice message.
	 * @param string $type    Notice type (success or error).
	 *
	 * @return never
	 */
	private static function redirect( string $code, string $message, string $type ): void {
		add_settings_error( 'scfw', $code, $message, $type );
		set_transient( 'settings_errors', get_settings_errors(), 30 );

		wp_safe_redirect( admin_url( self::PAGE_URL . '&settings-updated=true' ) );
		exit;
	}
}
