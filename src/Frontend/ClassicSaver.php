<?php
/**
 * Classic checkout saver.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldType;

/**
 * Persists validated checkout field values as WooCommerce order meta.
 *
 * Only order meta is written. No custom database tables are used.
 *
 * @since 0.0.1
 */
final class ClassicSaver {

	/**
	 * Save a field value to the order.
	 *
	 * An unchecked optional checkbox stores nothing: leaving the customer's
	 * choice implicit as "off" is the same state as never visiting the field,
	 * and an empty-string meta record would be indistinguishable presentation
	 * noise. A checked box stores `1` so the explicit choice round-trips.
	 *
	 * @since 0.0.1
	 *
	 * @param \WC_Order $order WooCommerce order.
	 * @param Field     $field Field definition.
	 * @param mixed     $value Validated value.
	 *
	 * @return void
	 */
	public function save( \WC_Order $order, Field $field, mixed $value ): void {
		$scalar = $this->toScalar( $value );

		if ( FieldType::CHECKBOX === $field->getType() && '' === $scalar ) {
			return;
		}

		$order->update_meta_data( '_scfw_' . $field->getId(), $scalar );
	}

	/**
	 * Normalize a value into a scalar string for storage.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Validated value.
	 *
	 * @return string Scalar representation.
	 */
	private function toScalar( mixed $value ): string {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}
}
