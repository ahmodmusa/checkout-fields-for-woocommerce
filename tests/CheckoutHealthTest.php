<?php
/**
 * Checkout Health Test Suite.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Admin\CheckoutHealth;
use SCFW\Core\Field\FieldType;

/**
 * Tests for CheckoutHealth diagnostic service.
 */
final class CheckoutHealthTest extends TestCase {

	/**
	 * Test that a healthy field configuration returns healthy status and 0 warnings.
	 */
	public function testHealthyConfiguration(): void {
		$config = array(
			'preferred_date' => array(
				'id'          => 'preferred_date',
				'label'       => 'Preferred Date',
				'type'        => FieldType::DATE->value,
				'enabled'     => true,
				'conditions'  => null,
			),
			'gift_wrap'      => array(
				'id'          => 'gift_wrap',
				'label'       => 'Gift Wrap',
				'type'        => FieldType::CHECKBOX->value,
				'enabled'     => true,
				'conditions'  => null,
			),
			'gift_message'   => array(
				'id'          => 'gift_message',
				'label'       => 'Gift Message',
				'type'        => FieldType::TEXTAREA->value,
				'enabled'     => true,
				'conditions'  => array(
					'field'    => 'gift_wrap',
					'operator' => 'equals',
					'value'    => 'yes',
				),
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_HEALTHY, $result['status'] );
		$this->assertEmpty( $result['warnings'] );
	}

	/**
	 * Test that an enabled field with an empty label produces a missing_label warning.
	 */
	public function testMissingLabelWarningOnEnabledField(): void {
		$config = array(
			'test_field' => array(
				'id'         => 'test_field',
				'label'      => '   ',
				'type'       => FieldType::TEXT->value,
				'enabled'    => true,
				'conditions' => null,
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_WARNING, $result['status'] );
		$this->assertCount( 1, $result['warnings'] );
		$this->assertSame( CheckoutHealth::CODE_MISSING_LABEL, $result['warnings'][0]['code'] );
		$this->assertSame( 'test_field', $result['warnings'][0]['field'] );
	}

	/**
	 * Test that disabled fields with empty labels do not trigger false positive warnings.
	 */
	public function testDisabledFieldWithEmptyLabelDoesNotWarn(): void {
		$config = array(
			'disabled_field' => array(
				'id'         => 'disabled_field',
				'label'      => '',
				'type'       => FieldType::TEXT->value,
				'enabled'    => false,
				'conditions' => null,
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_HEALTHY, $result['status'] );
		$this->assertEmpty( $result['warnings'] );
	}

	/**
	 * Test that a select field with zero valid options produces an empty_choice_options warning.
	 */
	public function testEmptySelectOptionsWarning(): void {
		$config = array(
			'time_slot' => array(
				'id'         => 'time_slot',
				'label'      => 'Time Slot',
				'type'       => FieldType::SELECT->value,
				'enabled'    => true,
				'options'    => array( '', '   ' ),
				'conditions' => null,
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_WARNING, $result['status'] );
		$this->assertCount( 1, $result['warnings'] );
		$this->assertSame( CheckoutHealth::CODE_EMPTY_CHOICE, $result['warnings'][0]['code'] );
		$this->assertSame( 'time_slot', $result['warnings'][0]['field'] );
	}

	/**
	 * Test that a radio field with zero valid options produces an empty_choice_options warning.
	 */
	public function testEmptyRadioOptionsWarning(): void {
		$config = array(
			'radio_choice' => array(
				'id'         => 'radio_choice',
				'label'      => 'Contact Choice',
				'type'       => FieldType::RADIO->value,
				'enabled'    => true,
				'options'    => array(),
				'conditions' => null,
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_WARNING, $result['status'] );
		$this->assertCount( 1, $result['warnings'] );
		$this->assertSame( CheckoutHealth::CODE_EMPTY_CHOICE, $result['warnings'][0]['code'] );
	}

	/**
	 * Test that choice fields with at least one valid option produce no warning.
	 */
	public function testValidChoiceOptionsProduceNoWarning(): void {
		$config = array(
			'time_slot' => array(
				'id'         => 'time_slot',
				'label'      => 'Time Slot',
				'type'       => FieldType::SELECT->value,
				'enabled'    => true,
				'options'    => array( 'Morning', 'Evening' ),
				'conditions' => null,
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_HEALTHY, $result['status'] );
		$this->assertEmpty( $result['warnings'] );
	}

	/**
	 * Test that a condition referencing a non-existent controller ID produces a broken_condition_reference warning.
	 */
	public function testBrokenConditionReferenceWarning(): void {
		$config = array(
			'gift_message' => array(
				'id'         => 'gift_message',
				'label'      => 'Gift Message',
				'type'       => FieldType::TEXTAREA->value,
				'enabled'    => true,
				'conditions' => array(
					'field'    => 'non_existent_controller',
					'operator' => 'equals',
					'value'    => 'yes',
				),
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_WARNING, $result['status'] );
		$this->assertCount( 1, $result['warnings'] );
		$this->assertSame( CheckoutHealth::CODE_BROKEN_CONDITION, $result['warnings'][0]['code'] );
		$this->assertSame( 'gift_message', $result['warnings'][0]['field'] );
	}

	/**
	 * Test that a self-referencing condition produces a self_referencing_condition warning.
	 */
	public function testSelfReferencingConditionWarning(): void {
		$config = array(
			'gift_message' => array(
				'id'         => 'gift_message',
				'label'      => 'Gift Message',
				'type'       => FieldType::TEXTAREA->value,
				'enabled'    => true,
				'conditions' => array(
					'field'    => 'gift_message',
					'operator' => 'equals',
					'value'    => 'yes',
				),
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_WARNING, $result['status'] );
		$this->assertCount( 1, $result['warnings'] );
		$this->assertSame( CheckoutHealth::CODE_SELF_CONDITION, $result['warnings'][0]['code'] );
	}

	/**
	 * Test that an unsupported condition operator produces an invalid_condition warning.
	 */
	public function testInvalidOperatorConditionWarning(): void {
		$config = array(
			'gift_wrap'    => array(
				'id'         => 'gift_wrap',
				'label'      => 'Gift Wrap',
				'type'       => FieldType::CHECKBOX->value,
				'enabled'    => true,
				'conditions' => null,
			),
			'gift_message' => array(
				'id'         => 'gift_message',
				'label'      => 'Gift Message',
				'type'       => FieldType::TEXTAREA->value,
				'enabled'    => true,
				'conditions' => array(
					'field'    => 'gift_wrap',
					'operator' => 'greater_than_unsupported',
					'value'    => 'yes',
				),
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_WARNING, $result['status'] );
		$this->assertCount( 1, $result['warnings'] );
		$this->assertSame( CheckoutHealth::CODE_INVALID_CONDITION, $result['warnings'][0]['code'] );
	}

	/**
	 * Test that a malformed non-array condition payload produces an invalid_condition warning.
	 */
	public function testMalformedConditionPayloadWarning(): void {
		$config = array(
			'gift_message' => array(
				'id'         => 'gift_message',
				'label'      => 'Gift Message',
				'type'       => FieldType::TEXTAREA->value,
				'enabled'    => true,
				'conditions' => 'invalid_non_array_string',
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_WARNING, $result['status'] );
		$this->assertCount( 1, $result['warnings'] );
		$this->assertSame( CheckoutHealth::CODE_INVALID_CONDITION, $result['warnings'][0]['code'] );
	}

	/**
	 * Test that multiple warnings are accumulated correctly across multiple fields.
	 */
	public function testMultipleWarningsAccumulation(): void {
		$config = array(
			'broken_field_1' => array(
				'id'         => 'broken_field_1',
				'label'      => '',
				'type'       => FieldType::SELECT->value,
				'enabled'    => true,
				'options'    => array(),
				'conditions' => null,
			),
			'broken_field_2' => array(
				'id'         => 'broken_field_2',
				'label'      => 'Field 2',
				'type'       => FieldType::TEXT->value,
				'enabled'    => true,
				'conditions' => array(
					'field'    => 'broken_field_2',
					'operator' => 'equals',
					'value'    => '1',
				),
			),
		);

		$result = CheckoutHealth::audit( $config );

		$this->assertSame( CheckoutHealth::STATUS_WARNING, $result['status'] );
		// broken_field_1 has missing_label and empty_choice_options; broken_field_2 has self_referencing_condition
		$this->assertCount( 3, $result['warnings'] );
		$codes = array_column( $result['warnings'], 'code' );
		$this->assertContains( CheckoutHealth::CODE_MISSING_LABEL, $codes );
		$this->assertContains( CheckoutHealth::CODE_EMPTY_CHOICE, $codes );
		$this->assertContains( CheckoutHealth::CODE_SELF_CONDITION, $codes );
	}

	/**
	 * Test that warning payloads have all required keys: code, severity, field, field_label, message, suggestion.
	 */
	public function testWarningPayloadStructure(): void {
		$config = array(
			'select_test' => array(
				'id'         => 'select_test',
				'label'      => 'Select Test',
				'type'       => FieldType::SELECT->value,
				'enabled'    => true,
				'options'    => array(),
				'conditions' => null,
			),
		);

		$result = CheckoutHealth::audit( $config );
		$this->assertCount( 1, $result['warnings'] );

		$warning = $result['warnings'][0];
		$this->assertArrayHasKey( 'code', $warning );
		$this->assertArrayHasKey( 'severity', $warning );
		$this->assertArrayHasKey( 'field', $warning );
		$this->assertArrayHasKey( 'field_label', $warning );
		$this->assertArrayHasKey( 'message', $warning );
		$this->assertArrayHasKey( 'suggestion', $warning );
		$this->assertSame( 'warning', $warning['severity'] );
	}
}
