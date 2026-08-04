<?php
/**
 * WooCommerce dependency checker.
 *
 * @package SCFW\Services
 */

declare(strict_types=1);

namespace SCFW\Services;

/**
 * Service that checks whether WooCommerce is active.
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
}
