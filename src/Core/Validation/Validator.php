<?php
/**
 * Field validator.
 *
 * @package SCFW\Core\Validation
 */

declare(strict_types=1);

namespace SCFW\Core\Validation;

use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldType;

/**
 * Validates a Field definition against a submitted value.
 *
 * The validator is independent of WordPress and WooCommerce. It never throws
 * exceptions for user input; it returns a {@see ValidationResult} instead.
 * InvalidArgumentException is reserved for programmer errors only.
 *
 * @since 0.0.1
 */
final class Validator {

	/**
	 * Boolean values accepted for a checkbox field.
	 *
	 * @var array<int, string|bool>
	 */
	private const CHECKBOX_VALUES = array(
		true,
		false,
		'1',
		'0',
		'on',
		'off',
	);

	/**
	 * Validate a field value.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Field definition.
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationResult Validation result.
	 */
	public function validate( Field $field, mixed $value ): ValidationResult {
		if ( $field->getRequired() && $this->isEmpty( $value, $field->getType() ) ) {
			return new ValidationResult(
				array(
					new ValidationError(
						'required',
						sprintf( '%s is required.', $field->getLabel() )
					),
				)
			);
		}

		if ( $this->isEmpty( $value, $field->getType() ) ) {
			return new ValidationResult();
		}

		$error = $this->validateValue( $field, $value );

		if ( null === $error ) {
			return new ValidationResult();
		}

		return new ValidationResult( array( $error ) );
	}

	/**
	 * Validate a non-empty field value by type.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Field definition.
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateValue( Field $field, mixed $value ): ?ValidationError {
		return match ( $field->getType() ) {
			FieldType::TEXT     => $this->validateText( $value ),
			FieldType::TEXTAREA => $this->validateTextarea( $value ),
			FieldType::EMAIL    => $this->validateEmail( $value ),
			FieldType::TEL      => $this->validateTel( $value ),
			FieldType::NUMBER   => $this->validateNumber( $value ),
			FieldType::DATE     => $this->validateDate( $value ),
			FieldType::CHECKBOX => $this->validateCheckbox( $value ),
			FieldType::RADIO    => $this->validateOption( $field, $value ),
			FieldType::SELECT   => $this->validateOption( $field, $value ),
		};
	}

	/**
	 * Validate a text field value is scalar.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateText( mixed $value ): ?ValidationError {
		if ( ! is_scalar( $value ) ) {
			return new ValidationError( 'text', 'The value must be text.' );
		}

		return null;
	}

	/**
	 * Validate a textarea field value is scalar.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateTextarea( mixed $value ): ?ValidationError {
		if ( ! is_scalar( $value ) ) {
			return new ValidationError( 'textarea', 'The value must be text.' );
		}

		return null;
	}

	/**
	 * Validate an email field value format.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateEmail( mixed $value ): ?ValidationError {
		if ( ! is_scalar( $value ) || false === filter_var( (string) $value, FILTER_VALIDATE_EMAIL ) ) {
			return new ValidationError( 'email', 'Please enter a valid email address.' );
		}

		return null;
	}

	/**
	 * Validate a telephone field value is scalar.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateTel( mixed $value ): ?ValidationError {
		if ( ! is_scalar( $value ) ) {
			return new ValidationError( 'tel', 'The value must be a telephone number.' );
		}

		return null;
	}

	/**
	 * Validate a number field value is numeric.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateNumber( mixed $value ): ?ValidationError {
		if ( ! is_numeric( $value ) ) {
			return new ValidationError( 'number', 'The value must be a number.' );
		}

		return null;
	}

	/**
	 * Validate a date field value is parseable.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateDate( mixed $value ): ?ValidationError {
		if ( ! is_scalar( $value ) || false === strtotime( (string) $value ) ) {
			return new ValidationError( 'date', 'Please enter a valid date.' );
		}

		return null;
	}

	/**
	 * Validate a checkbox field value is boolean.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateCheckbox( mixed $value ): ?ValidationError {
		if ( ! in_array( $value, self::CHECKBOX_VALUES, true ) ) {
			return new ValidationError( 'checkbox', 'The value must be a boolean.' );
		}

		return null;
	}

	/**
	 * Validate a choice field value exists among the field options.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Field definition.
	 * @param mixed $value Submitted value.
	 *
	 * @return ValidationError|null Validation error, or null when valid.
	 */
	private function validateOption( Field $field, mixed $value ): ?ValidationError {
		if ( ! is_scalar( $value ) || ! in_array( (string) $value, $field->getOptions(), true ) ) {
			return new ValidationError( 'invalid_option', 'The selected value is not allowed.' );
		}

		return null;
	}

	/**
	 * Determine whether a value is empty for a given field type.
	 *
	 * A checkbox is considered empty when unchecked; all other types are
	 * considered empty for null, empty strings and empty arrays.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed     $value Submitted value.
	 * @param FieldType $type  Field type.
	 *
	 * @return bool True when the value is empty, false otherwise.
	 */
	private function isEmpty( mixed $value, FieldType $type ): bool {
		if ( FieldType::CHECKBOX === $type ) {
			return ! in_array( $value, array( true, '1', 'on' ), true );
		}

		if ( null === $value ) {
			return true;
		}

		if ( is_string( $value ) ) {
			return '' === trim( $value );
		}

		if ( is_array( $value ) ) {
			return 0 === count( $value );
		}

		return false;
	}
}