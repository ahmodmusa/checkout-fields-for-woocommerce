<?php
/**
 * Classic checkout renderer.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Core\Field\Field;
use SCFW\Core\Rendering\RenderContext;
use SCFW\Core\Rendering\Renderer;
use SCFW\Core\Rendering\RenderResult;

/**
 * Renders checkout fields during the WooCommerce Classic Checkout.
 *
 * Implements the shared Core Renderer contract and delegates the markup to
 * the WooCommerce form field API so no rendering logic is duplicated.
 *
 * @since 0.0.1
 */
final class ClassicRenderer implements Renderer {

	/**
	 * Render a checkout field.
	 *
	 * @since 0.0.1
	 *
	 * @param Field         $field   Field definition.
	 * @param RenderContext $context Render context.
	 *
	 * @return RenderResult Render result.
	 */
	public function render( Field $field, RenderContext $context ): RenderResult {
		$value = $context->getValues()[ $field->getId() ] ?? $field->getDefaultValue();
		$args  = $this->fieldArgs( $field, $context );
		$html  = woocommerce_form_field( $field->getId(), $args, $value );

		return new RenderResult( (string) $html, array() );
	}

	/**
	 * Build the WooCommerce form field arguments for a field.
	 *
	 * @since 0.0.1
	 *
	 * @param Field         $field   Field definition.
	 * @param RenderContext $context Render context.
	 *
	 * @return array<string, mixed> WooCommerce form field arguments.
	 */
	private function fieldArgs( Field $field, RenderContext $context ): array {
		$args = array(
			'type'        => $field->getType()->value,
			'label'       => $field->getLabel(),
			'required'    => $field->getRequired(),
			'placeholder' => $field->getPlaceholder(),
			'description' => $field->getDescription(),
			'options'     => $field->getOptions(),
			'class'       => array( 'form-row-wide' ),
			'return'      => true,
		);

		if ( $context->getAttributes() ) {
			$args['custom_attributes'] = $context->getAttributes();
		}

		return $args;
	}
}
