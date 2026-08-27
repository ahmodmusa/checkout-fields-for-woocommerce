<?php
/**
 * Classic renderer field-argument tests.
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
use SCFW\Core\Rendering\RenderContext;
use SCFW\Frontend\ClassicRenderer;

/**
 * Verifies the WooCommerce form field arguments built for Classic checkout
 * fields, in particular the choice option mapping that keys each option by
 * its own string so the submitted value round-trips through validation.
 */
final class ClassicRendererTest extends TestCase {

	/**
	 * Invoke the private field-arguments builder.
	 *
	 * @param Field $field Core field definition.
	 *
	 * @return array<string, mixed> WooCommerce form field arguments.
	 */
	private function args( Field $field ): array {
		$method = new ReflectionMethod( ClassicRenderer::class, 'fieldArgs' );

		/** @var array<string, mixed> $args */
		$args = $method->invoke( new ClassicRenderer(), $field, new RenderContext() );

		return $args;
	}

	public function testRadioOptionsAreKeyedByOwnString(): void {
		$args = $this->args(
			FieldFactory::create( 'radio', 'Radio', FieldType::RADIO, false, '', '', array( 'Alu', 'Begun' ) )
		);

		$this->assertSame( 'radio', $args['type'] );
		$this->assertSame(
			array(
				'Alu'   => 'Alu',
				'Begun' => 'Begun',
			),
			$args['options']
		);
	}

	public function testRadioRowCarriesScopedAndRadioClasses(): void {
		$args = $this->args(
			FieldFactory::create( 'radio', 'Radio', FieldType::RADIO, false, '', '', array( 'Alu', 'Begun' ) )
		);

		$this->assertSame( array( 'form-row-wide', 'scfw-field', 'scfw-radio' ), $args['class'] );
	}

	public function testSelectRowCarriesScopedClass(): void {
		$args = $this->args(
			FieldFactory::create( 'select', 'Select', FieldType::SELECT, false, '', '', array( 'Number1', 'NUm2' ) )
		);

		$this->assertSame( array( 'form-row-wide', 'scfw-field', 'scfw-select' ), $args['class'] );
	}

	public function testTextRowCarriesScopedClass(): void {
		$args = $this->args(
			FieldFactory::create( 'notes', 'Notes', FieldType::TEXT )
		);

		$this->assertSame( array( 'form-row-wide', 'scfw-field', 'scfw-text' ), $args['class'] );
	}

	public function testEveryTypeCarriesScopedClass(): void {
		foreach ( FieldType::cases() as $type ) {
			$args = $this->args(
				FieldFactory::create( 'any', $type->value, $type, false, '', '', $type->value === 'radio' || $type->value === 'select' ? array( 'A', 'B' ) : array() )
			);

			$this->assertContains( 'scfw-field', $args['class'] );
			$this->assertContains( 'scfw-' . $type->value, $args['class'] );
		}
	}

	public function testSelectOptionsAreKeyedByOwnString(): void {
		$args = $this->args(
			FieldFactory::create( 'select', 'Select', FieldType::SELECT, false, '', '', array( 'Number1', 'NUm2' ) )
		);

		$this->assertSame( 'select', $args['type'] );
		$this->assertSame(
			array(
				'Number1' => 'Number1',
				'NUm2'    => 'NUm2',
			),
			$args['options']
		);
	}

	public function testChoiceOptionsKeyByStringForEmptyList(): void {
		$args = $this->args(
			FieldFactory::create( 'radio', 'Radio', FieldType::RADIO, false, '', '', array() )
		);

		$this->assertSame( array(), $args['options'] );
	}

	public function testTextFieldOptionsRemainList(): void {
		$args = $this->args(
			FieldFactory::create( 'notes', 'Notes', FieldType::TEXT )
		);

		$this->assertSame( 'text', $args['type'] );
		$this->assertSame( array(), $args['options'] );
	}

	public function testChoiceArgsCarryCoreConfig(): void {
		$args = $this->args(
			FieldFactory::create(
				'radio',
				'Radio',
				FieldType::RADIO,
				true,
				'Pick one',
				'Select a flavor',
				array( 'Alu', 'Begun' ),
				'Alu'
			)
		);

		$this->assertTrue( $args['required'] );
		$this->assertSame( 'Pick one', $args['placeholder'] );
		$this->assertSame( array( 'Alu' => 'Alu', 'Begun' => 'Begun' ), $args['options'] );
	}

	public function testRadioAndSelectStripDescription(): void {
		foreach ( array( FieldType::RADIO, FieldType::SELECT, FieldType::CHECKBOX, FieldType::EMAIL, FieldType::DATE ) as $type ) {
			$args = $this->args(
				FieldFactory::create( 'extra', $type->value, $type, false, '', 'A long helper text' )
			);

			$this->assertSame( '', $args['description'] );
		}
	}

	public function testTextAndTextareaKeepDescription(): void {
		foreach ( array( FieldType::TEXT, FieldType::TEXTAREA ) as $type ) {
			$args = $this->args(
				FieldFactory::create( 'extra', $type->value, $type, false, '', 'A long helper text' )
			);

			$this->assertSame( 'A long helper text', $args['description'] );
		}
	}

	/**
	 * Invoke the private required-select empty-option helper.
	 *
	 * @param string $html Existing markup.
	 * @return string Processed markup.
	 */
	private function emptyOption( string $html ): string {
		$method = new ReflectionMethod( ClassicRenderer::class, 'withEmptySelectOption' );

		/** @var string $result */
		$result = $method->invoke( null, $html );

		return $result;
	}

	public function testRequiredSelectGetsEmptyPlaceholderOption(): void {
		$html = '<select class="select " id="pick" name="pick"> <option value="A" >A</option><option value="B" >B</option> </select>';

		$result = $this->emptyOption( $html );

		$this->assertMatchesRegularExpression(
			'/<select[^>]*><option value=""><\/option>/i',
			$result
		);
		$this->assertSame( 1, preg_match( '/<option value="">/', $result ) );
	}

	public function testEmptyOptionLeavesNonSelectMarkupUntouched(): void {
		$this->assertSame( '<input type="text" />', $this->emptyOption( '<input type="text" />' ) );
	}

	public function testEmptyOptionIgnoredWhenNoSelectTag(): void {
		$this->assertSame( '<div>options</div>', $this->emptyOption( '<div>options</div>' ) );
	}
}