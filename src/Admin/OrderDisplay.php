<?php
/**
 * Admin order display.
 *
 * @package SCFW\Admin
 */

declare(strict_types=1);

namespace SCFW\Admin;

use SCFW\Core\Field\FieldRepository;
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
	 * Order meta key prefix for saved checkout field values.
	 *
	 * Mirrors the prefix used by the classic checkout saver so this class never
	 * re-implements storage rules; it only maps a field to its meta key.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const META_PREFIX = '_scfw_';

	/**
	 * Register the admin order display hook.
	 *
	 * The hook fires inside the WooCommerce order data meta box on the order
	 * edit screen and supports both order storage modes (HPOS and legacy).
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'woocommerce_admin_order_data_after_order_details', array( self::class, 'render' ), 10, 1 );
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

		$rows       = array();
		$known      = array();
		$metaValues = array();

		foreach ( $order->get_meta_data() as $meta ) {
			$data = $meta->get_data();
			$key  = isset( $data['key'] ) && is_scalar( $data['key'] ) ? (string) $data['key'] : '';

			if ( 0 !== strpos( $key, self::META_PREFIX ) ) {
				continue;
			}

			$id = substr( $key, strlen( self::META_PREFIX ) );

			if ( '' !== $id && isset( $data['value'] ) && is_scalar( $data['value'] ) ) {
				$metaValues[ $id ] = (string) $data['value'];
			}
		}

		foreach ( FieldRepository::all() as $field ) {
			$id = $field->getId();
			$known[ $id ] = true;

			if ( ! isset( $metaValues[ $id ] ) || '' === $metaValues[ $id ] ) {
				continue;
			}

			$rows[] = array(
				'label' => $field->getLabel(),
				'value' => $metaValues[ $id ],
			);
			unset( $metaValues[ $id ] );
		}

		foreach ( $metaValues as $id => $value ) {
			if ( '' === $value || isset( $known[ $id ] ) ) {
				continue;
			}

			$rows[] = array(
				'label' => ucwords( str_replace( '_', ' ', $id ) ),
				'value' => $value,
			);
		}

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
	 * @param array<int, array{label: string, value: string}> $rows Field rows.
	 *
	 * @return void
	 */
	private static function output( array $rows ): void {
		?>
		<h4><?php esc_html_e( 'Checkout Fields', 'checkout-fields-for-woocommerce' ); ?></h4>
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
