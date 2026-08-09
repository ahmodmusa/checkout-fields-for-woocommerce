<?php
/**
 * Block checkout coordinator.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Core\Field\DefaultFields;
use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldType;

/**
 * Coordinates the WooCommerce Block Checkout integration.
 *
 * The coordinator registers the Core field definitions with the official
 * WooCommerce Blocks additional checkout field API and mirrors the submitted
 * values onto the `_scfw_` order meta convention.
 *
 * Rendering, collection and lifecycle handling are provided by WooCommerce
 * itself once a field is registered. This class does not duplicate the
 * Classic Checkout pipeline and never writes legacy post meta directly.
 *
 * @since 0.0.1
 */
final class BlockCheckout {

	/**
	 * Namespace prefix prepended to Core field ids for the Blocks API.
	 *
	 * WooCommerce requires additional checkout field ids of the form
	 * namespace/name. The Core field id is used as the name and the plugin
	 * namespace is prepended here only.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const FIELD_NAMESPACE = 'scfw';

	/**
	 * Checkout location the registered field should be rendered in.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const LOCATION = 'order';

	/**
	 * Validator adapter.
	 *
	 * @since 0.0.1
	 *
	 * @var ClassicValidator
	 */
	private ClassicValidator $validator;

	/**
	 * Create a block checkout coordinator.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->validator = new ClassicValidator();
	}

	/**
	 * Register all Core field definitions with the Blocks API.
	 *
	 * Hooked to woocommerce_init.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public function registerField(): void {
		foreach ( DefaultFields::get() as $field ) {
			woocommerce_register_additional_checkout_field( $this->settings( $field ) );
		}
	}

	/**
	 * Sanitize a submitted block checkout field value.
	 *
	 * Used as the official sanitize_callback. The registered field options
	 * are mapped back to the Core field before the shared sanitization runs.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 * @param array $field Registered field options.
	 *
	 * @return string|bool Sanitized value.
	 */
	public function sanitizeField( mixed $value, array $field ): string|bool {
		$core = $this->coreField( $field );

		if ( null === $core ) {
			return '';
		}

		return $this->validator->sanitize( $core, $value );
	}

	/**
	 * Validate a field value with the shared Core Validator.
	 *
	 * Used as the official validate_callback.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 * @param array $field Registered field options.
	 *
	 * @return bool|\WP_Error True when valid, an error otherwise.
	 */
	public function validateField( mixed $value, array $field ): bool|\WP_Error {
		$core = $this->coreField( $field );

		if ( null === $core ) {
			return true;
		}

		$result = $this->validator->validate( $core, $value );

		if ( $result->isValid() ) {
			return true;
		}

		$errors = new \WP_Error();

		foreach ( $result->getErrors() as $error ) {
			$errors->add( 'scfw_' . $core->getId(), $error->getMessage() );
		}

		return $errors;
	}

	/**
	 * Persist the block checkout field values to the SCFW order meta keys.
	 *
	 * Hooked to woocommerce_store_api_checkout_update_order_from_request. The
	 * official Blocks API does not use the `_scfw_` convention, so the
	 * submitted values already validated on the route are mirrored onto the
	 * SCFW keys here. The order receives the meta through the standard
	 * WC_Data update flow, which is HPOS-compatible.
	 *
	 * @since 0.0.1
	 *
	 * @param \WC_Order          $order   Order being updated.
	 * @param \WP_REST_Request   $request Checkout request.
	 *
	 * @return void
	 */
	public function saveFields( \WC_Order $order, \WP_REST_Request $request ): void {
		$additional = isset( $request['additional_fields'] ) ? (array) $request['additional_fields'] : array();

		foreach ( DefaultFields::get() as $field ) {
			$id = $this->blockId( $field );

			if ( ! isset( $additional[ $id ] ) ) {
				continue;
			}

			$value  = $this->validator->sanitize( $field, $additional[ $id ] );
			$result = $this->validator->validate( $field, $value );

			if ( ! $result->isValid() ) {
				continue;
			}

			$order->update_meta_data( '_scfw_' . $field->getId(), $this->toScalar( $value ) );
		}
	}

	/**
	 * Build the Blocks API field settings from a Core field definition.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Core field definition.
	 *
	 * @return array<string, mixed> Blocks API field options.
	 */
	private function settings( Field $field ): array {
		return array(
			'id'                => $this->blockId( $field ),
			'label'             => $field->getLabel(),
			'optionalLabel'     => $field->getLabel(),
			'location'          => self::LOCATION,
			'type'              => $this->blockType( $field ),
			'required'          => $field->getRequired(),
			'sanitize_callback' => array( $this, 'sanitizeField' ),
			'validate_callback' => array( $this, 'validateField' ),
		);
	}

	/**
	 * Map a Core field type to a type the Blocks API supports.
	 *
	 * The Blocks API supports text, select and checkbox only. The Core
	 * TEXTAREA type is mapped to text so the Core definition remains
	 * unchanged while the block gets a compatible control. This is an
	 * adapter-level mapping and never changes the Core FieldType.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Core field definition.
	 *
	 * @return string Supported Blocks field type.
	 */
	private function blockType( Field $field ): string {
		return match ( $field->getType() ) {
			FieldType::SELECT   => 'select',
			FieldType::CHECKBOX => 'checkbox',
			default             => 'text',
		};
	}

	/**
	 * Build the namespaced block id for a Core field.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Core field definition.
	 *
	 * @return string Namespaced field id.
	 */
	private function blockId( Field $field ): string {
		return self::FIELD_NAMESPACE . '/' . $field->getId();
	}

	/**
	 * Resolve the Core field definition for registered field options.
	 *
	 * @since 0.0.1
	 *
	 * @param array $field Registered field options.
	 *
	 * @return Field|null Matching Core field, or null when unknown.
	 */
	private function coreField( array $field ): ?Field {
		$id = $field['id'] ?? '';

		foreach ( DefaultFields::get() as $core ) {
			if ( $this->blockId( $core ) === $id ) {
				return $core;
			}
		}

		return null;
	}

	/**
	 * Normalize a value into a scalar string for storage.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Validated value.
	 *
	 * @return string Scalar representation.
	 */
	private function toScalar( mixed $value ): string {
		if ( is_scalar( $value ) ) {
			return (string) $value;
		}

		return '';
	}
}
