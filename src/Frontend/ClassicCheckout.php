<?php
/**
 * Classic checkout coordinator.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldRepository;
use SCFW\Core\Rendering\RenderContext;
use SCFW\Core\Rendering\Renderer;
use SCFW\Services\FieldVisibility;

/**
 * Coordinates the Classic Checkout render, validate and save flow.
 *
 * The coordinator wires together ClassicRenderer, ClassicValidator and
 * ClassicSaver. It holds no business rules; it only moves data between the
 * shared Core components and WooCommerce.
 *
 * @since 0.0.1
 */
final class ClassicCheckout {

	/**
	 * Shared renderer.
	 *
	 * @since 0.0.1
	 *
	 * @var Renderer
	 */
	private Renderer $renderer;

	/**
	 * Classic validator.
	 *
	 * @since 0.0.1
	 *
	 * @var ClassicValidator
	 */
	private ClassicValidator $validator;

	/**
	 * Classic saver.
	 *
	 * @since 0.0.1
	 *
	 * @var ClassicSaver
	 */
	private ClassicSaver $saver;

	/**
	 * Create the classic checkout coordinator.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->renderer  = new ClassicRenderer();
		$this->validator = new ClassicValidator();
		$this->saver     = new ClassicSaver();
	}

	/**
	 * Render the checkout fields.
	 *
	 * Hooked to woocommerce_after_order_notes.
	 *
	 * @since 0.0.1
	 *
	 * @param \WC_Checkout $checkout WooCommerce checkout object.
	 *
	 * @return void
	 */
	public function renderFields( \WC_Checkout $checkout ): void {
		$config = FieldRepository::config();

		foreach ( FieldRepository::get() as $field ) {
			if ( ! FieldVisibility::isVisible( $field->getId(), $config[ $field->getId() ] ?? array() ) ) {
				continue;
			}

			$values  = array( $field->getId() => $this->submittedValue( array(), $field ) );
			$context = new RenderContext( $values );
			$result  = $this->renderer->render( $field, $context );

			echo $result->getHtml(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Validate the submitted checkout field values.
	 *
	 * Hooked to woocommerce_after_checkout_validation.
	 *
	 * @since 0.0.1
	 *
	 * @param array<string, mixed> $data   Posted checkout data.
	 * @param \WP_Error $errors Error collection.
	 *
	 * @return void
	 */
	public function validateFields( array $data, \WP_Error $errors ): void {
		$config = FieldRepository::config();

		foreach ( FieldRepository::get() as $field ) {
			if ( ! FieldVisibility::isVisible( $field->getId(), $config[ $field->getId() ] ?? array() ) ) {
				continue;
			}

			$value  = $this->sanitizeField( $data, $field );
			$result = $this->validator->validate( $field, $value );

			if ( $result->isValid() ) {
				continue;
			}

			foreach ( $result->getErrors() as $error ) {
				$errors->add( 'scfw_' . $field->getId(), $error->getMessage() );
			}
		}
	}

	/**
	 * Save the validated checkout field values as order meta.
	 *
	 * Hooked to woocommerce_checkout_create_order.
	 *
	 * @since 0.0.1
	 *
	 * @param \WC_Order $order WooCommerce order.
	 * @param array<string, mixed> $data  Posted checkout data.
	 *
	 * @return void
	 */
	public function saveFields( \WC_Order $order, array $data ): void {
		$config = FieldRepository::config();

		foreach ( FieldRepository::get() as $field ) {
			if ( ! FieldVisibility::isVisible( $field->getId(), $config[ $field->getId() ] ?? array() ) ) {
				continue;
			}

			$value  = $this->sanitizeField( $data, $field );
			$result = $this->validator->validate( $field, $value );

			if ( ! $result->isValid() ) {
				continue;
			}

			$this->saver->save( $order, $field, $value );
		}
	}

	/**
	 * Sanitize a field value from the posted data.
	 *
	 * @since 0.0.1
	 *
	 * @param array<string, mixed> $data  Posted checkout data.
	 * @param Field $field Field definition.
	 *
	 * @return string|bool Sanitized value.
	 */
	private function sanitizeField( array $data, Field $field ): string|bool {
		return $this->validator->sanitize( $field, $this->submittedValue( $data, $field ) );
	}

	/**
	 * Resolve the submitted value for a field.
	 *
	 * Prefers the normalized posted data and falls back to raw input and the
	 * field default. The checkout nonce is verified by WooCommerce before any
	 * of the hooks fire, so reading posted data here is safe.
	 *
	 * @since 0.0.1
	 *
	 * @param array<string, mixed> $data  Posted checkout data.
	 * @param Field $field Field definition.
	 *
	 * @return mixed Submitted or default value.
	 */
	private function submittedValue( array $data, Field $field ): mixed {
		$key = $field->getId();

		if ( isset( $data[ $key ] ) ) {
			return $data[ $key ];
		}

		// The checkout form nonce is verified by WooCommerce before this hook
		// fires, and the value is sanitized before validation and storage.
		// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$posted = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : null;
		// phpcs:enable WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( null !== $posted ) {
			return $posted;
		}

		return $field->getDefaultValue();
	}
}
