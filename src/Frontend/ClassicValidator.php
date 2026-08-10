<?php
/**
 * Classic checkout validator.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldType;
use SCFW\Core\Validation\ValidationResult;
use SCFW\Core\Validation\Validator;

/**
 * Adapts the shared Core Validator for the Classic Checkout.
 *
 * Sanitization happens here and validation is delegated to the shared
 * Validator so the validation rules are never duplicated.
 *
 * @since 0.0.1
 */
final class ClassicValidator {

	/**
	 * Shared field validator.
	 *
	 * @since 0.0.1
	 *
	 * @var Validator
	 */
	private Validator $validator;

	/**
	 * Create a classic validator.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {
		$this->validator = new Validator();
	}

	/**
	 * Sanitize a submitted value for a field.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Field definition.
	 * @param mixed $value Submitted value.
	 *
	 * @return string|bool Sanitized value.
	 */
	public function sanitize( Field $field, mixed $value ): string|bool {
		if ( null === $value || '' === $value ) {
			return '';
		}

		$scalar = is_scalar( $value ) ? (string) $value : '';

		return match ( $field->getType() ) {
			FieldType::CHECKBOX => in_array( $value, array( 'on', '1', true, 1 ), true ),
			FieldType::TEXTAREA => sanitize_textarea_field( $scalar ),
			FieldType::EMAIL    => sanitize_email( $scalar ),
			default             => sanitize_text_field( $scalar ),
		};
	}

	/**
	 * Validate a sanitized field value using the shared Validator.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Field definition.
	 * @param mixed $value Sanitized value.
	 *
	 * @return ValidationResult Validation result.
	 */
	public function validate( Field $field, mixed $value ): ValidationResult {
		return $this->validator->validate( $field, $value );
	}
}
