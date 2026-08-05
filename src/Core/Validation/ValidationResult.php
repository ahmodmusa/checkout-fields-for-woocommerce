<?php
/**
 * Validation result value object.
 *
 * @package SCFW\Core\Validation
 */

declare(strict_types=1);

namespace SCFW\Core\Validation;

/**
 * Immutable result of a single field validation.
 *
 * A result is created once with a list of errors and exposed only through
 * read-only methods. See {@see ValidationResult::isValid()} and
 * {@see ValidationResult::getErrors()}.
 *
 * @since 0.0.1
 */
final class ValidationResult {

	/**
	 * Collected validation errors.
	 *
	 * @var array<int, ValidationError>
	 */
	private array $errors;

	/**
	 * Create a new validation result.
	 *
	 * @since 0.0.1
	 *
	 * @param array<int, ValidationError> $errors Collected validation errors.
	 */
	public function __construct( array $errors = array() ) {
		$this->errors = $errors;
	}

	/**
	 * Get whether the validation passed.
	 *
	 * @since 0.0.1
	 *
	 * @return bool True when there are no errors, false otherwise.
	 */
	public function isValid(): bool {
		return 0 === count( $this->errors );
	}

	/**
	 * Get the collected validation errors.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, ValidationError> Collected validation errors.
	 */
	public function getErrors(): array {
		return $this->errors;
	}
}