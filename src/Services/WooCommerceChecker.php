<?php
/**
 * WooCommerce dependency checker.
 *
 * @package SCFW\Services
 */

declare(strict_types=1);

namespace SCFW\Services;

/**
 * Service that checks whether WooCommerce is active and declares feature
 * compatibility using the WooCommerce FeaturesUtil API.
 *
 * @since 0.0.1
 */
final class WooCommerceChecker {

	/**
	 * Check whether WooCommerce is active.
	 *
	 * @since 0.0.1
	 *
	 * @return bool True when WooCommerce is active, false otherwise.
	 */
	public static function isAvailable(): bool {
		return class_exists( 'WooCommerce' );
	}

	/**
	 * Declare plugin compatibility with WooCommerce features.
	 *
	 * Must run inside the before_woocommerce_init hook. The custom order
	 * tables (HPOS) feature is supported.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function declareCompatibility(): void {
		if ( ! class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', SCFW_FILE, true );
	}
}
