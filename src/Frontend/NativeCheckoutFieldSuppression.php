<?php
/**
 * Native checkout-field presentation suppression.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

/**
 * Prevents WooCommerce from presenting SCFW's Block Checkout fields twice.
 *
 * Block Checkout registers the fields with WooCommerce's Additional Checkout
 * Fields API and also mirrors their values to SCFW order metadata. SCFW's
 * order adapters remain the canonical presentation, so only the native
 * presentation of the `scfw/` namespace is suppressed.
 *
 * @since 0.0.1
 */
final class NativeCheckoutFieldSuppression {

	/**
	 * SCFW field namespace used by the WooCommerce Additional Checkout Fields API.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const FIELD_NAMESPACE = 'scfw/';

	/**
	 * Register native WooCommerce presentation filters.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_filter( 'woocommerce_filter_fields_for_order_confirmation', array( self::class, 'filterOrderConfirmation' ), 10, 5 );
		add_filter( 'woocommerce_admin_billing_fields', array( self::class, 'filterAdminFields' ), 20, 3 );
		add_filter( 'woocommerce_admin_shipping_fields', array( self::class, 'filterAdminFields' ), 20, 3 );
	}

	/**
	 * Hide only SCFW fields from WooCommerce confirmation and email output.
	 *
	 * @since 0.0.1
	 *
	 * @param bool                 $show    Whether WooCommerce should show the field.
	 * @param array<string, mixed> $field   Native field definition.
	 * @param array<string, mixed> $fields  All native field definitions.
	 * @param array<string, mixed> $context Native rendering context.
	 * @param object               $service Native checkout-field service.
	 * @return bool Whether WooCommerce should show the field.
	 */
	public static function filterOrderConfirmation( bool $show, array $field, array $fields, array $context, object $service ): bool {
		return self::isScfwField( $field['id'] ?? null ) ? false : $show;
	}

	/**
	 * Hide only SCFW fields from the native admin order fields.
	 *
	 * @since 0.0.1
	 *
	 * @param array<string, mixed> $fields  Native admin fields.
	 * @param mixed                $order   WooCommerce order.
	 * @param string               $context Admin field context.
	 * @return array<string, mixed> Filtered native admin fields.
	 */
	public static function filterAdminFields( array $fields, $order = null, string $context = 'edit' ): array {
		foreach ( array_keys( $fields ) as $key ) {
			if ( is_string( $key ) && self::isScfwField( self::nativeFieldId( $key ) ) ) {
				unset( $fields[ $key ] );
			}
		}

		return $fields;
	}

	/**
	 * Determine whether a native field belongs to SCFW.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $field_id Native field id.
	 * @return bool True for an SCFW field id.
	 */
	private static function isScfwField( mixed $field_id ): bool {
		return is_string( $field_id ) && 0 === strpos( $field_id, self::FIELD_NAMESPACE );
	}

	/**
	 * Normalize a native admin field key to its Additional Checkout Fields id.
	 *
	 * @since 0.0.1
	 *
	 * @param string $key Native admin field key.
	 * @return string Additional Checkout Fields id.
	 */
	private static function nativeFieldId( string $key ): string {
		$prefix = '_wc_other/';

		return 0 === strpos( $key, $prefix ) ? substr( $key, strlen( $prefix ) ) : $key;
	}
}
