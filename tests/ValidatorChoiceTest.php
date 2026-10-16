<?php
/**
 * Choice field validation tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldFactory;
use SCFW\Core\Field\FieldType;
use SCFW\Core\Validation\Validator;

/**
 * Verifies radio/select choice validation using the configured option lists
 * carried by the Field value object.
 */
final class ValidatorChoiceTest extends TestCase {

	/**
	 * Shared validator.
	 *
	 * @var Validator
	 */
	private Validator $validator;

	protected function setUp(): void {
		$this->validator = new Validator();
	}

	/**
	 * Build a non-required choice field.
	 *
	 * @param FieldType           $type    Choice type.
	 * @param array<int, string>  $options Configured options.
	 * @return Field Built field.
	 */
	private function choiceField( FieldType $type, array $options ): Field {
		return FieldFactory::create( 'size', 'Size', $type, false, '', '', $options );
	}

	public function testSelectAcceptsConfiguredOption(): void {
		$field = $this->choiceField( FieldType::SELECT, array( 'Small', 'Large' ) );

		$this->assertTrue( $this->validator->validate( $field, 'Small' )->isValid() );
	}

	public function testSelectRejectsUnknownOption(): void {
		$field = $this->choiceField( FieldType::SELECT, array( 'Small', 'Large' ) );

		$this->assertFalse( $this->validator->validate( $field, 'Extra Large' )->isValid() );
	}

	public function testRadioRejectsUnknownOption(): void {
		$field = $this->choiceField( FieldType::RADIO, array( 'A', 'B' ) );

		$this->assertFalse( $this->validator->validate( $field, 'C' )->isValid() );
	}

	public function testRadioAcceptsConfiguredOption(): void {
		$field = $this->choiceField( FieldType::RADIO, array( 'A', 'B' ) );

		$this->assertTrue( $this->validator->validate( $field, 'A' )->isValid() );
	}

	public function testOptionalChoiceAllowsEmptySelection(): void {
		$field = $this->choiceField( FieldType::RADIO, array( 'A', 'B' ) );

		$this->assertTrue( $this->validator->validate( $field, '' )->isValid() );
	}

	public function testRequiredChoiceRejectsEmptySelection(): void {
		$field = FieldFactory::create( 'size', 'Size', FieldType::SELECT, true, '', '', array( 'A', 'B' ) );

		$this->assertFalse( $this->validator->validate( $field, '' )->isValid() );
	}
}