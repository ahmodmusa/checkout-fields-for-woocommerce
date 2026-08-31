<?php
/**
 * Classic checkout frontend assets.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

/**
 * Enqueues the frontend stylesheet for the Classic Checkout renderer.
 *
 * The stylesheet ships layout rules for the native SCFW radio options only.
 * It loads exclusively on Classic (shortcode) checkout pages so the Block
 * Checkout and every other field type keep their default rendering.
 *
 * @since 0.0.1
 */
final class ClassicAssets {

	/**
	 * Enqueue the Classic checkout stylesheet.
	 *
	 * Hooked to wp_enqueue_scripts.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function enqueueAssets(): void {
		if ( is_admin() ) {
			return;
		}

		// Enqueue on checkout, order received (thank you), or my account view order pages.
		$is_scfw_page = is_checkout()
			|| ( function_exists( 'is_order_received_page' ) && is_order_received_page() )
			|| ( function_exists( 'is_view_order_page' ) && is_view_order_page() );

		if ( ! $is_scfw_page ) {
			$post = get_post();

			if ( ! $post || ! has_shortcode( $post->post_content, 'woocommerce_checkout' ) ) {
				return;
			}
		}

		$style_version = (string) filemtime( SCFW_PATH . 'assets/frontend/classic.css' );

		wp_enqueue_style( 'scfw-classic', SCFW_URL . 'assets/frontend/classic.css', array(), $style_version );
	}
}
