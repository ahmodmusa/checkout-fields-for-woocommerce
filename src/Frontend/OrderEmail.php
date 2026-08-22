<?php
/**
 * Order email adapter.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Services\OrderFieldPresentation;
use WC_Email;
use WC_Order;

/**
 * Displays saved checkout field values in WooCommerce order emails.
 *
 * Hooks the native `woocommerce_email_order_meta` action so the values appear
 * inside the order details section of every customer and administrative order
 * email that WooCommerce renders. The adapter is a thin WooCommerce layer: it
 * pulls the rows from the shared presentation service and only escapes and
 * prints them.
 *
 * Nothing is printed when no configured field has a saved value, so emails
 * without checkout-field data remain byte-for-byte unchanged.
 *
 * @since 0.0.1
 */
final class OrderEmail {

	/**
	 * Register the order email hook.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'woocommerce_email_order_meta', array( self::class, 'render' ), 10, 4 );
	}

	/**
	 * Render saved checkout field values into a WooCommerce order email.
	 *
	 * @since 0.0.1
	 *
	 * @param WC_Order    $order         WooCommerce order.
	 * @param bool        $sent_to_admin Whether the email is sent to an administrator.
	 * @param bool        $plain_text    Whether the email is plain text.
	 * @param WC_Email    $email         WooCommerce email object.
	 *
	 * @return void
	 */
	public static function render( WC_Order $order, bool $sent_to_admin, bool $plain_text, WC_Email $email ): void {
		$rows = OrderFieldPresentation::rowsFor( $order );

		if ( empty( $rows ) ) {
			return;
		}

		if ( $plain_text ) {
			self::outputPlain( $rows );
			return;
		}

		self::outputHtml( $rows );
	}

	/**
	 * Print the checkout fields section in HTML emails.
	 *
	 * @since 0.0.1
	 *
	 * @param array<int, array{id: string, label: string, value: string}> $rows Field rows.
	 *
	 * @return void
	 */
	private static function outputHtml( array $rows ): void {
		?>
		<p>
			<?php foreach ( $rows as $row ) : ?>
				<strong><?php echo esc_html( $row['label'] ); ?>:</strong>
				<?php echo esc_html( $row['value'] ); ?>
				<br />
			<?php endforeach; ?>
		</p>
		<?php
	}

	/**
	 * Print the checkout fields section in plain-text emails.
	 *
	 * @since 0.0.1
	 *
	 * @param array<int, array{id: string, label: string, value: string}> $rows Field rows.
	 *
	 * @return void
	 */
	private static function outputPlain( array $rows ): void {
		foreach ( $rows as $row ) {
			echo esc_html( $row['label'] ) . ':' . PHP_EOL;
			echo esc_html( $row['value'] ) . PHP_EOL;
		}
	}
}
