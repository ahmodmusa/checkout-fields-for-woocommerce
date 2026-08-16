<?php
/**
 * Configured field repository.
 *
 * @package SCFW\Core\Field
 */

declare(strict_types=1);

namespace SCFW\Core\Field;

/**
 * Resolves the active checkout field definitions from the stored plugin
 * configuration.
 *
 * The repository is the single source that turns the `scfw_options.fields`
 * configuration (or the default fields when no configuration exists yet) into
 * Core Field value objects. Classic Checkout, Block Checkout and the admin
 * order display all consume the same resolved list, so the configured order
 * and the enabled/disabled state stay identical across every consumer. The
 * repository also owns the field configuration schema rules: exposed entries
 * are always normalized through the same id, type and text sanitization.
 *
 * The data model stays unchanged: configuration lives in the WordPress
 * `scfw_options` option and no custom tables or second option are introduced.
 *
 * @since 0.0.1
 */
final class FieldRepository {

	/**
	 * Settings option name.
	 *
	 * Mirrors src/Admin/Settings.php. The option is the single storage for all
	 * plugin configuration, so this constant is kept private and literal.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'scfw_options';

	/**
	 * Option key holding the configured fields map.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const FIELDS_KEY = 'fields';

	/**
	 * Get the checkout fields that should be rendered and saved.
	 *
	 * Only enabled fields are returned, in their configured order. When no
	 * field configuration exists yet the default fields are returned so the
	 * initial behavior remains unchanged.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, Field> Enabled field definitions in configured order.
	 */
	public static function get(): array {
		if ( ! self::isEnabled() ) {
			return array();
		}

		$fields = array();

		foreach ( self::config() as $id => $settings ) {
			if ( empty( $settings['enabled'] ) ) {
				continue;
			}

			$fields[] = self::build( $id, $settings );
		}

		return $fields;
	}

	/**
	 * Get every configured field, including disabled ones.
	 *
	 * Used by the admin builder so disabled fields stay visible and editable.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, Field> All configured field definitions.
	 */
	public static function all(): array {
		$fields = array();

		foreach ( self::config() as $id => $settings ) {
			$fields[] = self::build( $id, $settings );
		}

		return $fields;
	}

	/**
	 * Determine whether checkout field functionality is enabled.
	 *
	 * The repository is the shared configuration boundary for both checkout
	 * adapters, so the global switch is evaluated here instead of being
	 * duplicated in Classic and Block Checkout.
	 *
	 * @since 0.0.1
	 *
	 * @return bool True when checkout fields are enabled.
	 */
	public static function isEnabled(): bool {
		$options = get_option( self::OPTION_NAME, array() );

		return ! is_array( $options ) || ! isset( $options['enabled'] ) || ! empty( $options['enabled'] );
	}

	/**
	 * Get the effective field configuration.
	 *
	 * Returns the normalized stored configuration or, when no configuration is
	 * stored yet, the default field configuration. The returned map is ordered
	 * and keyed by field id. The array order is the configured order consumed
	 * by every checkout implementation.
	 *
	 * @since 0.0.1
	 *
	 * @return array<string, array{id: string, label: string, type: string, required: bool, enabled: bool, rules: array<int|string, mixed>}> Field configuration.
	 */
	public static function config(): array {
		$options = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $options ) || ! isset( $options[ self::FIELDS_KEY ] ) || ! is_array( $options[ self::FIELDS_KEY ] ) ) {
			return self::defaultConfig();
		}

		return self::sanitizeFields( $options[ self::FIELDS_KEY ] );
	}

	/**
	 * Normalize a raw field configuration into the canonical ordered map.
	 *
	 * Invalid entries are skipped so a corrupted option value or malformed
	 * request can never break the checkout. The canonical map is ordered by
	 * the position of the entries in the input.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $fields Raw field configuration.
	 *
	 * @return array<string, array{id: string, label: string, type: string, required: bool, enabled: bool, rules: array<int|string, mixed>}> Field configuration.
	 */
	public static function sanitizeFields( mixed $fields ): array {
		$clean = array();

		if ( ! is_array( $fields ) ) {
			return $clean;
		}

		foreach ( $fields as $entry ) {
			$normalized = self::normalizeFieldEntry( $entry );

			if ( null === $normalized || isset( $clean[ $normalized['id'] ] ) ) {
				continue;
			}

			$clean[ $normalized['id'] ] = $normalized;
		}

		return $clean;
	}

	/**
	 * Validate and normalize a complete field configuration.
	 *
	 * Unlike the tolerant runtime sanitizer, this method rejects the complete
	 * configuration when any entry is malformed so admin mutations cannot
	 * silently save a partial configuration.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $fields Field configuration to validate.
	 * @return array<string, array{id: string, label: string, type: string, required: bool, enabled: bool, rules: array<int|string, mixed>}>|null Normalized fields, or null when invalid.
	 */
	public static function validateFields( mixed $fields ): ?array {
		if ( ! is_array( $fields ) ) {
			return null;
		}

		$clean = array();

		foreach ( $fields as $entry ) {
			$normalized = self::normalizeFieldEntry( $entry, true );

			if ( null === $normalized || isset( $clean[ $normalized['id'] ] ) ) {
				return null;
			}

			$clean[ $normalized['id'] ] = $normalized;
		}

		return $clean;
	}

	/**
	 * Normalize a single field configuration entry.
	 *
	 * Applies the schema rules for a field entry: a valid id, a supported type
	 * from the Core FieldType enum and a non-empty label. An invalid entry
	 * resolves to null so callers can reject or skip it.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $entry  Raw field configuration entry.
	 * @param bool  $strict Require every schema key and strict boolean values.
	 *
	 * @return array{id: string, label: string, type: string, required: bool, enabled: bool, rules: array<int|string, mixed>}|null Normalized entry, or null when invalid.
	 */
	public static function normalizeFieldEntry( mixed $entry, bool $strict = false ): ?array {
		if ( ! is_array( $entry ) ) {
			return null;
		}

		if ( $strict && array_diff( array( 'id', 'label', 'type', 'required', 'enabled' ), array_keys( $entry ) ) ) {
			return null;
		}

		$id = '';

		if ( isset( $entry['id'] ) && is_scalar( $entry['id'] ) ) {
			$id = sanitize_key( (string) $entry['id'] );
		}

		if ( '' === $id || ! preg_match( '/^[a-z0-9_-]+$/', (string) $id ) ) {
			return null;
		}

		$raw_type = isset( $entry['type'] ) && is_scalar( $entry['type'] ) ? (string) $entry['type'] : '';
		$type     = FieldType::tryFrom( $raw_type );

		if ( null === $type ) {
			return null;
		}

		$label = '';

		if ( isset( $entry['label'] ) && is_scalar( $entry['label'] ) ) {
			$label = sanitize_text_field( (string) $entry['label'] );
		}

		if ( '' === $label && $strict ) {
			return null;
		}

		if ( '' === $label ) {
			$label = $id;
		}

		if ( $strict && ( ! self::isBooleanValue( $entry['required'] ) || ! self::isBooleanValue( $entry['enabled'] ) ) ) {
			return null;
		}

		$rules = array();

		if ( array_key_exists( 'rules', $entry ) ) {
			$raw = $entry['rules'];

			if ( is_array( $raw ) ) {
				$rules = $raw;
			} elseif ( is_string( $raw ) ) {
				$decoded = json_decode( $raw, true );

				if ( is_array( $decoded ) ) {
					$rules = $decoded;
				} elseif ( $strict ) {
					return null;
				}
			} elseif ( $strict ) {
				return null;
			}
		}

		return array(
			'id'       => $id,
			'label'    => $label,
			'type'     => $type->value,
			'required' => ! empty( $entry['required'] ),
			'enabled'  => ! empty( $entry['enabled'] ) || ! isset( $entry['enabled'] ),
			'rules'    => $rules,
		);
	}

	/**
	 * Check the scalar boolean representation used by the settings schema.
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
	 * Generate a unique, normalized field id from a label.
	 *
	 * Keeps the id compatible with the `_scfw_` order meta convention and the
	 * `scfw/` block field namespace. Existing ids are never reused.
	 *
	 * @since 0.0.1
	 *
	 * @param string                  $label   Field label.
	 * @param array<string, mixed>    $existing Existing field ids.
	 *
	 * @return string Unique field id.
	 */
	public static function generateId( string $label, array $existing ): string {
		$base = sanitize_key( str_replace( '-', '_', sanitize_title( (string) $label ) ) );

		if ( '' === $base ) {
			$base = 'field';
		}

		$id     = $base;
		$suffix = 2;

		while ( isset( $existing[ $id ] ) ) {
			$id = $base . '_' . $suffix;
			++$suffix;
		}

		return $id;
	}

	/**
	 * Build the default field configuration.
	 *
	 * The existing hardcoded delivery instructions field is the initial
	 * configured field, enabled by default, so the behavior stays unchanged
	 * until an administrator saves a configuration.
	 *
	 * @since 0.0.1
	 *
	 * @return array<string, array{id: string, label: string, type: string, required: bool, enabled: bool, rules: array<int|string, mixed>}> Field configuration.
	 */
	private static function defaultConfig(): array {
		$config = array();

		foreach ( DefaultFields::get() as $field ) {
			$config[ $field->getId() ] = array(
				'id'       => $field->getId(),
				'label'    => $field->getLabel(),
				'type'     => $field->getType()->value,
				'required' => $field->getRequired(),
				'enabled'  => true,
				'rules'    => array(),
			);
		}

		return $config;
	}

	/**
	 * Build a Field value object from normalized configuration.
	 *
	 * @since 0.0.1
	 *
	 * @param string $id       Field id.
	 * @param array{id: string, label: string, type: string, required: bool, enabled: bool} $settings Normalized field settings.
	 *
	 * @return Field Built field definition.
	 */
	private static function build( string $id, array $settings ): Field {
		return FieldFactory::create(
			$id,
			(string) $settings['label'],
			FieldType::tryFrom( (string) $settings['type'] ) ?? FieldType::TEXT,
			(bool) $settings['required']
		);
	}
}
