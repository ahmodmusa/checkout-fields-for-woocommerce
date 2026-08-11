<?php
/**
 * WooCommerce order REST response adapter.
 *
 * @package SCFW\REST
 */

declare(strict_types=1);

namespace SCFW\REST;

use SCFW\Services\OrderFieldPresentation;
use WC_Order;
use WP_REST_Request;
use WP_REST_Response;

/**
 * Adds authorized SCFW field values to the native WooCommerce order response.
 *
 * The native WooCommerce order endpoint performs authorization before this
 * response filter runs. This adapter only adds the shared presentation rows;
 * it does not register a route or expose generic order metadata.
 *
 * @since 0.0.1
 */
final class OrderResponse {

	/**
	 * Register the native WooCommerce order response filter.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		add_filter( 'woocommerce_rest_prepare_shop_order_object', array( self::class, 'addFields' ), 10, 3 );
	}

	/**
	 * Add SCFW fields to an authorized order response.
	 *
	 * @since 0.0.1
	 *
	 * @param WP_REST_Response $response Prepared WooCommerce response.
	 * @param WC_Order         $order    WooCommerce order.
	 * @param WP_REST_Request  $request  REST request.
	 * @return WP_REST_Response Updated response.
	 */
	public static function addFields( WP_REST_Response $response, WC_Order $order, WP_REST_Request $request ): WP_REST_Response {
		$data                 = $response->get_data();
		$data['scfw_fields']  = OrderFieldPresentation::rowsFor( $order );
		$response->set_data( $data );

		return $response;
	}
}
