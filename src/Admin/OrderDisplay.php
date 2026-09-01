<?php
/**
 * Admin order display.
 *
 * @package SCFW\Admin
 */

declare(strict_types=1);

namespace SCFW\Admin;

use SCFW\Core\Field\FieldDestination;
use SCFW\Services\OrderFieldPresentation;
use WC_Order;


/**
 * Displays saved checkout field values on the WooCommerce admin order screen.
 *
 * The saver writes every checkout field value to WooCommerce order meta under
 * the _scfw_ prefix. This class renders those values on the order edit screen.
 *
 * It reads only through the WooCommerce order object (get_meta) so the same
 * code works with HPOS and the legacy post storage. It never reads posts or
 * the legacy wp_postmeta tables directly.
 *
 * @since 0.0.1
 */
final class OrderDisplay {

	/**
	 * Register the admin order display hook.
	 *
	 * The hook fires inside the WooCommerce order data meta box after the columns
	 * on the order edit screen and supports both order storage modes (HPOS and legacy).
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( self::class, 'render' ), 10, 1 );
	}

	/**
	 * Render the saved checkout field values on the admin order screen.
	 *
	 * The section only renders when the current user can manage orders. Every
	 * dynamic value is escaped before output. Fields without a saved value are
	 * omitted from the display.
	 *
	 * @since 0.0.1
	 *
	 * @param WC_Order $order WooCommerce order.
	 *
	 * @return void
	 */
	public static function render( WC_Order $order ): void {
		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			return;
		}

		$rows = OrderFieldPresentation::rowsFor( $order, FieldDestination::ADMIN_ORDER );

		if ( empty( $rows ) ) {
			return;
		}

		self::output( $rows );
	}

	/**
	 * Print the checkout fields section.
	 *
	 * Hooked to `woocommerce_admin_order_data_after_shipping_address`, which fires
	 * inside `.order_data_column_shipping` within `.order_data_column_container`.
	 *
	 * By closing the shipping column and the column container prior to output,
	 * `.scfw-admin-order-fields` renders as a direct child of `#order_data` and a
	 * direct sibling of `.order_data_column_container`, spanning the full width of
	 * the Order Data meta box above the bottom of the container.
	 *
	 * @since 0.0.1
	 *
	 * @param array<int, array{id: string, label: string, value: string}> $rows Field rows.
	 *
	 * @return void
	 */
	private static function output( array $rows ): void {
		?>
					</div><!-- /.order_data_column_shipping -->
				</div><!-- /.order_data_column_container -->
				<div class="scfw-admin-order-fields" style="clear:both; width:100%; box-sizing:border-box; margin:24px 0 0 0; padding:20px 16px 0 16px; border-top:1px solid #e5e5e5; display:block;">
					<h3 style="margin:0 0 12px; font-size:14px; font-weight:600; color:#1d2327; display:flex; align-items:center; gap:6px;">
						<span class="dashicons dashicons-clipboard" style="font-size:18px; width:18px; height:18px; color:#5925dc;" aria-hidden="true"></span>
						<?php esc_html_e( 'Checkout Fields', 'checkout-fields-for-woocommerce' ); ?>
					</h3>
					<div class="scfw-admin-order-fields-card" style="background:#ffffff; border:1px solid #ccd0d4; border-radius:6px; overflow:hidden; box-shadow:0 1px 1px rgba(0,0,0,0.04);">
						<table style="width:100%; border-collapse:collapse; text-align:left; font-size:13px; margin:0;">
							<tbody>
								<?php foreach ( $rows as $index => $row ) : ?>
									<tr style="border-bottom:<?php echo $index < count( $rows ) - 1 ? '1px solid #f0f0f1' : 'none'; ?>;">
										<th scope="row" style="padding:10px 16px; width:30%; min-width:180px; font-weight:600; color:#50575e; background:#fafafa; border:none; vertical-align:top;">
											<?php echo esc_html( $row['label'] ); ?>
										</th>
										<td style="padding:10px 16px; color:#1d2327; background:#ffffff; border:none; vertical-align:top; word-break:break-word;">
											<?php echo esc_html( $row['value'] ); ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="order_data_column_container" style="display:none;">
					<div class="order_data_column order_data_column_shipping">
		<?php
	}
}
