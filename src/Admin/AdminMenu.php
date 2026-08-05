<?php
/**
 * Admin menu.
 *
 * @package SCFW\Admin
 */

declare(strict_types=1);

namespace SCFW\Admin;

/**
 * Registers the plugin admin menu.
 *
 * @since 0.0.1
 */
final class AdminMenu {

	/**
	 * Register the plugin admin menu.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_menu_page(
			__( 'Checkout Fields', 'checkout-fields-for-woocommerce' ),
			__( 'Checkout Fields', 'checkout-fields-for-woocommerce' ),
			'manage_options',
			'scfw',
			array( self::class, 'render' ),
			'dashicons-feedback',
			56
		);
	}

	/**
	 * Render the plugin admin page.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function render(): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Checkout Fields', 'checkout-fields-for-woocommerce' ); ?></h1>

			<?php settings_errors( 'scfw' ); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'scfw' );
				do_settings_sections( 'scfw' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}