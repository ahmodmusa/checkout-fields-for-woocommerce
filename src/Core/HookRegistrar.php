<?php
/**
 * Hook registrar.
 *
 * @package SCFW\Core
 */

declare(strict_types=1);

namespace SCFW\Core;

use SCFW\Admin\AdminMenu;
use SCFW\Admin\OrderDisplay;
use SCFW\Admin\Settings;
use SCFW\Frontend\BlockCheckoutBootstrap;
use SCFW\Frontend\CheckoutBootstrap;
use SCFW\Services\WooCommerceChecker;

/**
 * Central registrar for WordPress hooks.
 *
 * @since 0.0.1
 */
final class HookRegistrar {

	/**
	 * Register all plugin hooks.
	 *
	 * Admin hooks remain registered regardless of the WooCommerce
	 * availability. WooCommerce-dependent checkout hooks are registered only
	 * when WooCommerce is active.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'admin_menu', array( AdminMenu::class, 'register' ) );
		add_action( 'admin_init', array( Settings::class, 'register' ) );

		if ( WooCommerceChecker::isAvailable() ) {
			CheckoutBootstrap::register();
			BlockCheckoutBootstrap::register();
			OrderDisplay::register();
		}
	}
}
