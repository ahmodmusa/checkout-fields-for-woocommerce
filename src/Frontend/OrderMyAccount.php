<?php
/**
 * My Account order adapter.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Services\OrderFieldPresentation;
use WC_Order;

/**
 * Displays saved checkout field values in the customer's My Account order view.
 *
 * The native `woocommerce_view_order` action runs from WooCommerce's
 * authenticated view-order template after the order capability check. This
 * adapter repeats that native check before reading the order through the
 * WooCommerce API and delegates field resolution to the shared presentation
 * service.
 *
 * @since 0.0.1
 */
final class OrderMyAccount {

	/**
	 * Register the My Account order-view hook.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'woocommerce_view_order', array( self::class, 'render' ), 20, 1 );
	}

	/**
	 * Render saved checkout field values for an authorized order.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $order_id WooCommerce order id.
	 *
	 * @return void
	 */
	public static function render( $order_id ): void {
		if ( ! is_int( $order_id ) && ! is_string( $order_id ) ) {
			return;
		}

		if ( ! current_user_can( 'view_order', $order_id ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( ! $order instanceof WC_Order ) {
			return;
		}

		$rows = OrderFieldPresentation::rowsFor( $order );

		if ( empty( $rows ) ) {
			return;
		}

		self::output( $rows );
	}

	/**
	 * Print the checkout fields section.
	 *
	 * @since 0.0.1
	 *
	 * @param array<int, array{id: string, label: string, value: string}> $rows Field rows.
	 *
	 * @return void
	 */
	private static function output( array $rows ): void {
		?>
		<h2><?php esc_html_e( 'Checkout Fields', 'checkout-fields-for-woocommerce' ); ?></h2>
		<p>
			<?php foreach ( $rows as $row ) : ?>
				<strong><?php echo esc_html( $row['label'] ); ?>:</strong>
				<?php echo esc_html( $row['value'] ); ?>
				<br />
			<?php endforeach; ?>
		</p>
		<?php
	}
}
