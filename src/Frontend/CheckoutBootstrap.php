<?php
/**
 * Classic checkout bootstrap.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

/**
 * Registers every WooCommerce Classic Checkout hook.
 *
 * The bootstrap owns no business logic, rendering, or validation. It only
 * wires the WooCommerce lifecycle in to the ClassicCheckout coordinator.
 *
 * @since 0.0.1
 */
final class CheckoutBootstrap {

	/**
	 * Register the WooCommerce Classic Checkout hooks.
	 *
	 * Render, validate, save and asset loading are the only hooks registered.
	 * Nothing else, so only the Classic Checkout path is touched.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		$checkout = new ClassicCheckout();

		add_action( 'woocommerce_after_order_notes', array( $checkout, 'renderFields' ) );
		add_action( 'woocommerce_after_checkout_validation', array( $checkout, 'validateFields' ), 10, 2 );
		add_action( 'woocommerce_checkout_create_order', array( $checkout, 'saveFields' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( ClassicAssets::class, 'enqueueAssets' ) );
	}
}
