<?php
/**
 * Hook registrar.
 *
 * @package SCFW\Core
 */

declare(strict_types=1);

namespace SCFW\Core;

use SCFW\Admin\AdminMenu;
use SCFW\Admin\Settings;

/**
 * Central registrar for WordPress hooks.
 *
 * @since 0.0.1
 */
final class HookRegistrar {

	/**
	 * Register all plugin hooks.
	 *
	 * The admin hooks remain registered regardless of the WooCommerce
	 * availability. WooCommerce-dependent hooks are not registered here.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'admin_menu', array( AdminMenu::class, 'register' ) );
		add_action( 'admin_init', array( Settings::class, 'register' ) );
	}
}
