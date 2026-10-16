<?php
/**
 * Condition evaluator service.
 *
 * @package SCFW\Services
 */

declare(strict_types=1);

namespace SCFW\Services;

/**
 * Single semantic source of truth for evaluating conditional visibility rules.
 *
 * Evaluates whether a field's condition is met against a map of submitted values
 * or field state. Supports 'equals' and 'not_equals' operators.
 *
 * @since 0.0.1
 */
final class ConditionEvaluator {

	public const OPERATOR_EQUALS     = 'equals';
	public const OPERATOR_NOT_EQUALS = 'not_equals';

	/**
	 * Get supported condition operators.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, string> Supported operators.
	 */
	public static function supportedOperators(): array {
		return array(
			self::OPERATOR_EQUALS,
			self::OPERATOR_NOT_EQUALS,
		);
	}

	/**
	 * Normalize a raw condition payload.
	 *
	 * @since 0.0.1
	 *
	 * @param mixed       $raw     Raw condition data.
	 * @param string|null $self_id Optional field id to prevent self-referencing conditions.
	 *
	 * @return array{field: string, operator: string, value: string}|null Normalized condition or null if invalid/disabled.
	 */
	public static function normalize( mixed $raw, ?string $self_id = null ): ?array {
		if ( ! is_array( $raw ) ) {
			return null;
		}

		$field    = isset( $raw['field'] ) && is_scalar( $raw['field'] ) ? sanitize_key( (string) $raw['field'] ) : '';
		$operator = isset( $raw['operator'] ) && is_scalar( $raw['operator'] ) ? sanitize_key( (string) $raw['operator'] ) : '';
		$value    = isset( $raw['value'] ) && is_scalar( $raw['value'] ) ? sanitize_text_field( (string) $raw['value'] ) : '';

		if ( '' === $field ) {
			return null;
		}

		if ( null !== $self_id && $field === $self_id ) {
			return null;
		}

		if ( ! in_array( $operator, self::supportedOperators(), true ) ) {
			return null;
		}

		return array(
			'field'    => $field,
			'operator' => $operator,
			'value'    => $value,
		);
	}

	/**
	 * Evaluate a condition against a dataset (e.g. submitted $_POST data or form state).
	 *
	 * When no condition is present (null or empty), the field is unconditionally visible (returns true).
	 *
	 * Checkbox values normalize to 'yes'/'no' or '1'/'0'.
	 *
	 * @since 0.0.1
	 *
	 * @param array{field: string, operator: string, value: string}|null $condition Normalized condition.
	 * @param array<string, mixed>                                      $values    Data map keyed by field id.
	 *
	 * @return bool True if condition is met (or no condition), false otherwise.
	 */
	public static function isMet( ?array $condition, array $values ): bool {
		if ( null === $condition || empty( $condition['field'] ) ) {
			return true;
		}

		$field_id = (string) $condition['field'];
		$operator = (string) $condition['operator'];
		$expected = (string) ( $condition['value'] ?? '' );

		$actual_raw = $values[ $field_id ] ?? '';
		$actual     = self::normalizeValue( $actual_raw );

		$expected_norm = self::normalizeValue( $expected );

		$matches = ( $actual === $expected_norm );

		if ( self::OPERATOR_EQUALS === $operator ) {
			return $matches;
		}

		if ( self::OPERATOR_NOT_EQUALS === $operator ) {
			return ! $matches;
		}

		return true;
	}

	/**
	 * Normalize a comparison value for robust matching (e.g. checkbox '1' vs 'yes' or booleans).
	 *
	 * @since 0.0.1
	 *
	 * @param mixed $val Value to normalize.
	 *
	 * @return string Normalized string representation.
	 */
	public static function normalizeValue( mixed $val ): string {
		if ( true === $val || 1 === $val || '1' === $val ) {
			return 'yes';
		}

		if ( false === $val || 0 === $val || '0' === $val ) {
			return 'no';
		}

		if ( ! is_scalar( $val ) ) {
			return '';
		}

		$str = strtolower( trim( (string) $val ) );

		if ( 'yes' === $str || 'on' === $str ) {
			return 'yes';
		}

		if ( 'no' === $str || 'off' === $str ) {
			return 'no';
		}

		return $str;
	}
}
