<?php
/**
 * Field Preview Renderer unit tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Admin\FieldPreviewRenderer;

/**
 * Tests for FieldPreviewRenderer markup generation across all 9 field types,
 * required state, placeholders, descriptions, options, conditions, and disabled state.
 */
final class FieldPreviewRendererTest extends TestCase {

	public function testPreviewRendersTextTypeWithPlaceholder(): void {
		$field = array(
			'id'          => 'custom_text',
			'label'       => 'Custom Text Field',
			'type'        => 'text',
			'required'    => true,
			'enabled'     => true,
			'placeholder' => 'Enter text here',
			'default'     => 'Initial Value',
		);

		$html = FieldPreviewRenderer::render( $field );

		$this->assertStringContainsString( 'Custom Text Field', $html );
		$this->assertStringContainsString( 'class="required"', $html );
		$this->assertStringContainsString( 'placeholder="Enter text here"', $html );
		$this->assertStringContainsString( 'value="Initial Value"', $html );
		$this->assertStringContainsString( 'type="text"', $html );
	}

	public function testPreviewRendersTextareaWithDescription(): void {
		$field = array(
			'id'          => 'delivery_notes',
			'label'       => 'Delivery Notes',
			'type'        => 'textarea',
			'required'    => false,
			'enabled'     => true,
			'placeholder' => 'Add delivery instructions',
			'description' => 'Leave at the front porch if not home.',
			'default'     => '',
		);

		$html = FieldPreviewRenderer::render( $field );

		$this->assertStringContainsString( 'Delivery Notes', $html );
		$this->assertStringNotContainsString( 'class="required"', $html );
		$this->assertStringContainsString( '<textarea', $html );
		$this->assertStringContainsString( 'placeholder="Add delivery instructions"', $html );
		$this->assertStringContainsString( 'Leave at the front porch if not home.', $html );
	}

	public function testPreviewRendersSelectWithOptions(): void {
		$field = array(
			'id'          => 'time_slot',
			'label'       => 'Delivery Time Slot',
			'type'        => 'select',
			'required'    => true,
			'enabled'     => true,
			'placeholder' => 'Choose a slot',
			'options'     => array( 'Morning (9am-12pm)', 'Afternoon (1pm-5pm)' ),
			'default'     => 'Morning (9am-12pm)',
		);

		$html = FieldPreviewRenderer::render( $field );

		$this->assertStringContainsString( 'Delivery Time Slot', $html );
		$this->assertStringContainsString( '<select', $html );
		$this->assertStringContainsString( 'Choose a slot', $html );
		$this->assertStringContainsString( '<option value="Morning (9am-12pm)"', $html );
		$this->assertStringContainsString( 'selected', $html );
		$this->assertStringContainsString( '<option value="Afternoon (1pm-5pm)"', $html );
	}

	public function testPreviewRendersSelectWithEmptyOptionsNotice(): void {
		$field = array(
			'id'      => 'empty_select',
			'label'   => 'Empty Choice',
			'type'    => 'select',
			'options' => array(),
		);

		$html = FieldPreviewRenderer::render( $field );

		$this->assertStringContainsString( 'No options configured yet.', $html );
	}

	public function testPreviewRendersRadioGroup(): void {
		$field = array(
			'id'      => 'contact_pref',
			'label'   => 'Preferred Contact',
			'type'    => 'radio',
			'options' => array( 'Email', 'Phone', 'WhatsApp' ),
			'default' => 'Email',
		);

		$html = FieldPreviewRenderer::render( $field );

		$this->assertStringContainsString( 'Preferred Contact', $html );
		$this->assertStringContainsString( 'type="radio"', $html );
		$this->assertStringContainsString( 'value="Email"', $html );
		$this->assertStringContainsString( 'value="Phone"', $html );
		$this->assertStringContainsString( 'value="WhatsApp"', $html );
	}

	public function testPreviewRendersCheckboxType(): void {
		$field = array(
			'id'      => 'gift_wrap',
			'label'   => 'Add Gift Wrapping',
			'type'    => 'checkbox',
			'default' => 'yes',
		);

		$html = FieldPreviewRenderer::render( $field );

		$this->assertStringContainsString( 'type="checkbox"', $html );
		$this->assertStringContainsString( 'Add Gift Wrapping', $html );
		$this->assertStringContainsString( 'checked', $html );
	}

	public function testPreviewRendersNumberDateEmailTelTypes(): void {
		$types = array( 'number', 'date', 'email', 'tel' );
		foreach ( $types as $t ) {
			$field = array(
				'id'    => 'field_' . $t,
				'label' => 'Label ' . $t,
				'type'  => $t,
			);
			$html  = FieldPreviewRenderer::render( $field );
			$this->assertStringContainsString( 'type="' . $t . '"', $html );
		}
	}

	public function testPreviewRendersConditionalBadge(): void {
		$field = array(
			'id'         => 'gift_msg',
			'label'      => 'Gift Message',
			'type'       => 'text',
			'conditions' => array(
				'field'    => 'gift_wrap',
				'operator' => 'equals',
				'value'    => 'yes',
			),
		);

		$html = FieldPreviewRenderer::render( $field );

		$this->assertStringContainsString( 'scfw-preview-condition-tag', $html );
		$this->assertStringContainsString( 'Conditional: gift_wrap equals yes', $html );
	}

	public function testPreviewRendersDisabledState(): void {
		$field = array(
			'id'      => 'disabled_field',
			'label'   => 'Inactive Field',
			'type'    => 'text',
			'enabled' => false,
		);

		$html = FieldPreviewRenderer::render( $field );

		$this->assertStringContainsString( 'scfw-preview--disabled', $html );
		$this->assertStringContainsString( 'Field Disabled', $html );
	}
}
