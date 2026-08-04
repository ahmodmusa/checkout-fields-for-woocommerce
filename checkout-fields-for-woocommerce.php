<?php

declare(strict_types=1);

/**
 * Plugin Name: Checkout Fields for WooCommerce
 * Plugin URI: https://github.com/ahmodmusa/wc-checkout-fields
 * Description: Add custom checkout fields to WooCommerce Classic and Block Checkout.
 * Version: 0.0.1
 * Requires at least: 6.7
 * Tested up to: 6.7
 * Requires PHP: 8.2
 * Author: Ahmod Musa
 * Author URI: https://ahmodmusa.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: checkout-fields-for-woocommerce
 * Domain Path: /languages
 * WC requires at least: 9
 * WC tested up to: 9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SCFW_VERSION' ) ) {
	define( 'SCFW_VERSION', '0.0.1' );
}

if ( ! defined( 'SCFW_FILE' ) ) {
	define( 'SCFW_FILE', __FILE__ );
}

if ( ! defined( 'SCFW_PATH' ) ) {
	define( 'SCFW_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'SCFW_URL' ) ) {
	define( 'SCFW_URL', plugin_dir_url( __FILE__ ) );
}
