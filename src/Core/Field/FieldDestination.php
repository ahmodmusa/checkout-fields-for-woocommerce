<?php
/**
 * Field destination enum.
 *
 * @package SCFW\Core\Field
 */

declare(strict_types=1);

namespace SCFW\Core\Field;

/**
 * Supported checkout field presentation destinations.
 *
 * Defines where a field's collected data surfaces across the order lifecycle.
 * REST API is intentionally excluded as it represents a separate data
 * integration layer.
 *
 * @since 0.0.1
 */
enum FieldDestination : string {

	case ADMIN_ORDER = 'admin_order';

	case THANK_YOU = 'thank_you';

	case MY_ACCOUNT = 'my_account';

	case CUSTOMER_EMAIL = 'customer_email';

	case ADMIN_EMAIL = 'admin_email';

	/**
	 * Get all valid destination string values.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, string> List of all destination values.
	 */
	public static function all(): array {
		return array(
			self::ADMIN_ORDER->value,
			self::THANK_YOU->value,
			self::MY_ACCOUNT->value,
			self::CUSTOMER_EMAIL->value,
			self::ADMIN_EMAIL->value,
		);
	}

	/**
	 * Normalize a list of raw destination strings.
	 *
	 * Filters out invalid or duplicate values and returns a sorted, unique list
	 * of valid destination strings.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $destinations Raw destinations payload.
	 *
	 * @return array<int, string> Normalized destination values.
	 */
	public static function normalize( mixed $destinations ): array {
		if ( ! is_array( $destinations ) ) {
			return self::all();
		}

		$valid      = self::all();
		$normalized = array();

		foreach ( $destinations as $destination ) {
			if ( is_scalar( $destination ) && in_array( (string) $destination, $valid, true ) ) {
				$normalized[ (string) $destination ] = true;
			}
		}

		return array_values( array_intersect( $valid, array_keys( $normalized ) ) );
	}
}
