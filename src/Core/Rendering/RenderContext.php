<?php
/**
 * Render context value object.
 *
 * @package SCFW\Core\Rendering
 */

declare(strict_types=1);

namespace SCFW\Core\Rendering;

/**
 * Immutable context passed to a renderer.
 *
 * The context carries submitted values and per-field attributes into the
 * rendering layer without coupling it to WordPress or WooCommerce.
 *
 * @since 0.0.1
 */
final class RenderContext {

	/**
	 * Submitted values keyed by field id.
	 *
	 * @var array<string, mixed>
	 */
	private array $values;

	/**
	 * Additional attributes keyed by attribute name.
	 *
	 * @var array<string, string>
	 */
	private array $attributes;

	/**
	 * Create a new render context.
	 *
	 * @since 0.0.1
	 *
	 * @param array<string, mixed>  $values     Submitted values keyed by field id.
	 * @param array<string, string> $attributes Additional attributes keyed by attribute name.
	 */
	public function __construct(
		array $values = array(),
		array $attributes = array()
	) {
		$this->values     = $values;
		$this->attributes = $attributes;
	}

	/**
	 * Get the submitted values keyed by field id.
	 *
	 * @since 0.0.1
	 *
	 * @return array<string, mixed> Submitted values keyed by field id.
	 */
	public function getValues(): array {
		return $this->values;
	}

	/**
	 * Get the additional attributes keyed by attribute name.
	 *
	 * @since 0.0.1
	 *
	 * @return array<string, string> Additional attributes keyed by attribute name.
	 */
	public function getAttributes(): array {
		return $this->attributes;
	}
}