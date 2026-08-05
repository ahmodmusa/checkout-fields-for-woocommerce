<?php
/**
 * Renderer interface.
 *
 * @package SCFW\Core\Rendering
 */

declare(strict_types=1);

namespace SCFW\Core\Rendering;

use SCFW\Core\Field\Field;

/**
 * Contract for rendering checkout fields.
 *
 * Implementations are shared by Classic Checkout and Block Checkout.
 *
 * @since 0.0.1
 */
interface Renderer {

	/**
	 * Render a checkout field.
	 *
	 * @since 0.0.1
	 *
	 * @param Field        $field   Field definition.
	 * @param RenderContext $context Render context.
	 *
	 * @return RenderResult Render result.
	 */
	public function render( Field $field, RenderContext $context ): RenderResult;
}