<?php
/**
 * Validation error value object.
 *
 * @package SCFW\Core\Validation
 */

declare(strict_types=1);

namespace SCFW\Core\Validation;

/**
 * Immutable value object describing a single validation error.
 *
 * Errors are created once via the constructor and exposed only through
 * getters. See {@see ValidationError::getCode()} and
 * {@see ValidationError::getMessage()}.
 *
 * @since 0.0.1
 */
final class ValidationError {

	/**
	 * Error code.
	 *
	 * @var string
	 */
	private string $code;

	/**
	 * Human-readable error message.
	 *
	 * @var string
	 */
	private string $message;

	/**
	 * Create a new validation error.
	 *
	 * @since 0.0.1
	 *
	 * @param string $code    Error code.
	 * @param string $message Human-readable error message.
	 */
	public function __construct( string $code, string $message ) {
		$this->code    = $code;
		$this->message = $message;
	}

	/**
	 * Get the error code.
	 *
	 * @since 0.0.1
	 *
	 * @return string Error code.
	 */
	public function getCode(): string {
		return $this->code;
	}

	/**
	 * Get the human-readable error message.
	 *
	 * @since 0.0.1
	 *
	 * @return string Human-readable error message.
	 */
	public function getMessage(): string {
		return $this->message;
	}
}