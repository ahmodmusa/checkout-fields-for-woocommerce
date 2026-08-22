<?php
/**
 * Classic checkout renderer.
 *
 * @package SCFW\Frontend
 */

declare(strict_types=1);

namespace SCFW\Frontend;

use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldType;
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

		if ( FieldType::SELECT === $field->getType() && $field->getRequired() ) {
			$html = self::withEmptySelectOption( (string) $html );
		}

		return new RenderResult( (string) $html, array() );
	}

	/**
	 * Insert an empty placeholder option into a required select.
	 *
	 * Without a placeholder a required select silently pre-selects its first
	 * real option, because the browser submits the option that is visible when
	 * the customer never actively chooses. The leading empty option forces an
	 * explicit selection so the server-side required validation stays
	 * meaningful. The placeholder is never stored as a value.
	 *
	 * @since 0.0.1
	 *
	 * @param string $html Rendered select markup.
	 *
	 * @return string Markup with the empty placeholder option inserted.
	 */
	private static function withEmptySelectOption( string $html ): string {
		if ( false === strpos( $html, '<select' ) ) {
			return $html;
		}

		return (string) preg_replace( '/<select\b[^>]*>/i', '$0<option value=""></option>', $html, 1 );
	}

	/**
	 * Build the WooCommerce form field arguments for a field.
	 *
	 * Every SCFW row additionally carries a `scfw-field` class and a per-type
	 * `scfw-<type>` class. The markup classes are purely additive and keep the
	 * native WooCommerce form-field structure and accessibility attributes
	 * intact; they exist so the scoped Classic stylesheet can polish SCFW rows
	 * without touching unrelated checkout fields or the Block Checkout.
	 *
	 * @since 0.0.1
	 *
	 * @param Field         $field   Field definition.
	 * @param RenderContext $context Render context.
	 *
	 * @return array<string, mixed> WooCommerce form field arguments.
	 */
	private function fieldArgs( Field $field, RenderContext $context ): array {
		$class = array( 'form-row-wide', 'scfw-field', 'scfw-' . $field->getType()->value );

		$args = array(
			'type'        => $field->getType()->value,
			'label'       => $field->getLabel(),
			'required'    => $field->getRequired(),
			'placeholder' => $field->getPlaceholder(),
			'description' => $this->descriptionFor( $field ),
			'options'     => $this->choiceOptions( $field ),
			'class'       => $class,
			'return'      => true,
		);

		if ( $context->getAttributes() ) {
			$args['custom_attributes'] = $context->getAttributes();
		}

		return $args;
	}

	/**
	 * Resolve the description shown for a field.
	 *
	 * The baseline shows the description only for text and textarea fields.
	 * The stored description of every type is kept for data integrity, but
	 * choice fields and the boolean checkbox never print it on the Classic
	 * Checkout.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Field definition.
	 *
	 * @return string Description, or an empty string for non-text types.
	 */
	private function descriptionFor( Field $field ): string {
		$type = $field->getType();

		if ( FieldType::TEXT !== $type && FieldType::TEXTAREA !== $type ) {
			return '';
		}

		return $field->getDescription();
	}

	/**
	 * Map the Core choice options for the WooCommerce form field API.
	 *
	 * WooCommerce renders each option's array key as the input value and its
	 * value as the label. Core choice options are single strings that act as
	 * both the submitted value and the label, so the option string must be the
	 * key for the submitted value to round-trip through validation and
	 * storage. Non-choice fields keep their options untouched.
	 *
	 * @since 0.0.1
	 *
	 * @param Field $field Field definition.
	 *
	 * @return array<int|string, string> Map of option value to option label.
	 */
	private function choiceOptions( Field $field ): array {
		if ( FieldType::RADIO !== $field->getType() && FieldType::SELECT !== $field->getType() ) {
			return $field->getOptions();
		}

		$options = $field->getOptions();
		$mapped  = array_combine( $options, $options );

		return false === $mapped ? array() : $mapped;
	}
}
