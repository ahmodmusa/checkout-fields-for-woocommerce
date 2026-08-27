<?php
/**
 * Block checkout settings tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldFactory;
use SCFW\Core\Field\FieldType;
use SCFW\Frontend\BlockCheckout;

/**
 * Verifies the Blocks API field settings built from Core field definitions,
 * in particular the native select options mapping.
 */
final class BlockCheckoutSettingsTest extends TestCase {

	/**
	 * Invoke the private settings builder for a field.
	 *
	 * @param Field $field Core field definition.
	 *
	 * @return array<string, mixed> Blocks API field options.
	 */
	private function settings( Field $field ): array {
		$method = new ReflectionMethod( BlockCheckout::class, 'settings' );

		return $method->invoke( new BlockCheckout(), $field );
	}

	public function testSelectSettingsIncludeNativeOptions(): void {
		$field = FieldFactory::create(
			'delivery',
			'Delivery Slot',
			FieldType::SELECT,
			true,
			'',
			'',
			array( 'Morning', 'Evening' )
		);

		$settings = $this->settings( $field );

		$this->assertSame( 'scfw/delivery', $settings['id'] );
		$this->assertSame( 'select', $settings['type'] );
		$this->assertTrue( $settings['required'] );
		$this->assertSame(
			array(
				array( 'value' => 'Morning', 'label' => 'Morning' ),
				array( 'value' => 'Evening', 'label' => 'Evening' ),
			),
			$settings['options']
		);
	}

	public function testTextSettingsDoNotIncludeOptions(): void {
		$settings = $this->settings(
			FieldFactory::create( 'notes', 'Notes', FieldType::TEXT )
		);

		$this->assertSame( 'text', $settings['type'] );
		$this->assertArrayNotHasKey( 'options', $settings );
	}

	public function testCheckboxSettingsDoNotIncludeOptions(): void {
		$settings = $this->settings(
			FieldFactory::create( 'agree', 'Agree', FieldType::CHECKBOX )
		);

		$this->assertSame( 'checkbox', $settings['type'] );
		$this->assertArrayNotHasKey( 'options', $settings );
	}

	public function testDateSettingsRegisterAsTextWithNativeDateMarker(): void {
		$settings = $this->settings(
			FieldFactory::create( 'born', 'Date of Birth', FieldType::DATE )
		);

		$this->assertSame( 'text', $settings['type'] );
		$this->assertSame( array( 'data-scfw-type' => 'date' ), $settings['attributes'] );
		$this->assertArrayNotHasKey( 'options', $settings );
	}

	public function testNonDateSettingsDoNotCarryAttributes(): void {
		$text = $this->settings(
			FieldFactory::create( 'notes', 'Notes', FieldType::TEXT )
		);
		$box  = $this->settings(
			FieldFactory::create( 'agree', 'Agree', FieldType::CHECKBOX )
		);

		$this->assertArrayNotHasKey( 'attributes', $text );
		$this->assertArrayNotHasKey( 'attributes', $box );
	}

	public function testRadioMapsToNativeSelectWithOptions(): void {
		$settings = $this->settings(
			FieldFactory::create( 'color', 'Color', FieldType::RADIO, true, '', '', array( 'Red', 'Blue' ) )
		);

		$this->assertSame( 'scfw/color', $settings['id'] );
		$this->assertSame( 'select', $settings['type'] );
		$this->assertTrue( $settings['required'] );
		$this->assertSame(
			array(
				array( 'value' => 'Red', 'label' => 'Red' ),
				array( 'value' => 'Blue', 'label' => 'Blue' ),
			),
			$settings['options']
		);
	}

	public function testOptionalRadioMapsToNativeSelectWithOptions(): void {
		$settings = $this->settings(
			FieldFactory::create( 'shade', 'Shade', FieldType::RADIO, false, '', '', array( 'Light', 'Dark' ) )
		);

		$this->assertSame( 'select', $settings['type'] );
		$this->assertFalse( $settings['required'] );
		$this->assertSame(
			array(
				array( 'value' => 'Light', 'label' => 'Light' ),
				array( 'value' => 'Dark', 'label' => 'Dark' ),
			),
			$settings['options']
		);
	}
}
