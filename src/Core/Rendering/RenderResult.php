<?php
/**
 * Render result value object.
 *
 * @package SCFW\Core\Rendering
 */

declare(strict_types=1);

namespace SCFW\Core\Rendering;

/**
 * Immutable result returned by a renderer.
 *
 * The result carries the generated markup and any assets required by the
 * rendered field.
 *
 * @since 0.0.1
 */
final class RenderResult {

	/**
	 * Generated markup.
	 *
	 * @var string
	 */
	private string $html;

	/**
	 * Assets required by the rendered field.
	 *
	 * @var array<int, string>
	 */
	private array $assets;

	/**
	 * Create a new render result.
	 *
	 * @since 0.0.1
	 *
	 * @param string            $html   Generated markup.
	 * @param array<int, string> $assets Assets required by the rendered field.
	 */
	public function __construct(
		string $html = '',
		array $assets = array()
	) {
		$this->html   = $html;
		$this->assets = $assets;
	}

	/**
	 * Get the generated markup.
	 *
	 * @since 0.0.1
	 *
	 * @return string Generated markup.
	 */
	public function getHtml(): string {
		return $this->html;
	}

	/**
	 * Get the assets required by the rendered field.
	 *
	 * @since 0.0.1
	 *
	 * @return array<int, string> Assets required by the rendered field.
	 */
	public function getAssets(): array {
		return $this->assets;
	}
}