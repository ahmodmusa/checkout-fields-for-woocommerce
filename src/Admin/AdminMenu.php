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
		add_menu_page( 'Checkout Fields', 'Checkout Fields', 'manage_options', 'scfw', array( self::class, 'render' ), 'dashicons-feedback', 56 );
	}

	/**
	 * Render the plugin admin page.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function render(): void {
		echo '<div class="wrap">' . "\n";
		echo '    <h1>Checkout Fields</h1>' . "\n";
		echo '</div>';
	}
}
