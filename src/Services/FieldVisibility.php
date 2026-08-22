<?php
/**
 * Field visibility extension point.
 *
 * @package SCFW\Services
 */

declare(strict_types=1);

namespace SCFW\Services;

/**
 * Resolves the shared checkout field visibility decision.
 *
 * Free ships no rule engine and never evaluates conditional rules. The
 * `scfw_field_visibility` filter is the intentional Free extension point that
 * a Pro extension registers against: it receives the field id and the opaque
 * field configuration entry (including any preserved `rules` data) and may
 * return false to hide the field.
 *
 * With no filter registered the decision defaults to true, so the existing
 * enabled gates in FieldRepository remain the only authority and every
 * phase 1-5 consumer behaves exactly as before.
 *
 * @since 0.0.1
 */
final class FieldVisibility {

	/**
	 * Visibility decision filter name.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const FILTER = 'scfw_field_visibility';

	/**
	 * Determine whether a checkout field should be visible.
	 *
	 * The shared decision is resolved exactly once per field by the checkout
	 * coordinators; this service never performs rule evaluation itself.
	 *
	 * @since 0.0.1
	 *
	 * @param string                    $field_id     Checkout field id.
	 * @param array<int|string, mixed>  $field_config Normalized field configuration entry.
	 *
	 * @return bool True when the field should be visible.
	 */
	public static function isVisible( string $field_id, array $field_config ): bool {
		return (bool) apply_filters( self::FILTER, true, $field_id, $field_config );
	}
}
