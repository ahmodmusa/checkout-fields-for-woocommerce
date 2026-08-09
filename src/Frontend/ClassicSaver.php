<?php
/**
 * Classic checkout saver.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Core\Field\Field;

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
	 * @since 0.0.1
	 *
	 * @param \WC_Order $order WooCommerce order.
	 * @param Field     $field Field definition.
	 * @param mixed     $value Validated value.
	 *
	 * @return void
	 */
	public function save( \WC_Order $order, Field $field, mixed $value ): void {
		$order->update_meta_data( '_scfw_' . $field->getId(), $this->toScalar( $value ) );
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
