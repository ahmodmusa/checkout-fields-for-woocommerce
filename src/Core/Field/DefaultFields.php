<?php
/**
 * Default fields provider.
 *
 * @package SCFW\Core\Field
 */

declare(strict_types=1);

namespace SCFW\Core\Field;

/**
 * Provides the default checkout field definitions.
 *
 * @since 0.0.1
 */
final class DefaultFields {

	/**
	 * Get the default field definitions.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, Field> Default field definitions.
	 */
	public static function get(): array {
		return array();
	}
}