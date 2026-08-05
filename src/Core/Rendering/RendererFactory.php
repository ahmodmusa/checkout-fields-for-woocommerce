<?php
/**
 * Renderer factory.
 *
 * @package SCFW\Core\Rendering
 */

declare(strict_types=1);

namespace SCFW\Core\Rendering;

use LogicException;

/**
 * Creates the proper renderer implementation.
 *
 * Renderer implementations do not exist yet, so creating one is intentionally
 * unavailable until a future milestone provides them.
 *
 * @since 0.0.1
 */
final class RendererFactory {

	/**
	 * Get the renderer implementation for the given field.
	 *
	 * No renderer implementation exists at this milestone.
	 *
	 * @since 0.0.1
	 *
	 * @throws LogicException When no renderer implementation is available.
	 *
	 * @return Renderer Renderer implementation.
	 */
	public static function getRenderer(): Renderer {
		throw new LogicException( 'No renderer implementation is available yet.' );
	}
}