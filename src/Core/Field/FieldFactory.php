<?php
/**
 * Field factory.
 *
 * @package SCFW\Core\Field
 */

declare(strict_types=1);

namespace SCFW\Core\Field;

use InvalidArgumentException;

/**
 * Creates and validates Field value objects.
 *
 * The factory is independent of WordPress and WooCommerce. It is the only
 * entry point that validates field arguments before a Field is constructed.
 *
 * @since 0.0.1
 */
final class FieldFactory {

	/**
	 * Create a new field.
	 *
	 * @since 0.0.1
	 *
	 * @param string              $id           Field identifier.
	 * @param string              $label        Human-readable label.
	 * @param FieldType           $type         Field type.
	 * @param bool                $required     Whether the field is required.
	 * @param string              $placeholder  Input placeholder text.
	 * @param string              $description  Field description.
	 * @param array<int, string>  $options      Selectable options for choice-based fields.
	 * @param string|array<int, string> $defaultValue Default value.
	 * @param array<int, string>        $destinations Enabled presentation destinations.
	 * @param array{field: string, operator: string, value: string}|null $conditions Conditional visibility rule.
	 *
	 * @throws InvalidArgumentException When the field identifier or label is empty.
	 *
	 * @return Field Created field.
	 */
	public static function create(
		string $id,
		string $label,
		FieldType $type,
		bool $required = false,
		string $placeholder = '',
		string $description = '',
		array $options = array(),
		string|array $defaultValue = '',
		array $destinations = array(),
		?array $conditions = null
	): Field {
		self::validateId( $id );
		self::validateLabel( $label );

		return new Field(
			$id,
			$label,
			$type,
			$required,
			$placeholder,
			$description,
			$options,
			$defaultValue,
			$destinations,
			$conditions
		);
	}



	/**
	 * Validate the field identifier.
	 *
	 * @since 0.0.1
	 *
	 * @param string $id Field identifier.
	 *
	 * @throws InvalidArgumentException When the field identifier is empty.
	 *
	 * @return void
	 */
	private static function validateId( string $id ): void {
		if ( '' === $id ) {
			throw new InvalidArgumentException( 'Field id must not be empty.' );
		}
	}

	/**
	 * Validate the field label.
	 *
	 * @since 0.0.1
	 *
	 * @param string $label Human-readable label.
	 *
	 * @throws InvalidArgumentException When the field label is empty.
	 *
	 * @return void
	 */
	private static function validateLabel( string $label ): void {
		if ( '' === $label ) {
			throw new InvalidArgumentException( 'Field label must not be empty.' );
		}
	}
}