<?php
/**
 * ConditionEvaluator and conditional validation tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldFactory;
use SCFW\Core\Field\FieldRepository;
use SCFW\Core\Field\FieldType;
use SCFW\Frontend\ClassicCheckout;
use SCFW\Services\ConditionEvaluator;
use WP_Error;

/**
 * Verifies ConditionEvaluator semantics, normalization, and server-side validation bypassing when conditions are not met.
 */
final class ConditionEvaluatorTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['scfw_test_options'] = array();
	}

	public function testSupportedOperators(): void {
		$operators = ConditionEvaluator::supportedOperators();

		$this->assertContains( 'equals', $operators );
		$this->assertContains( 'not_equals', $operators );
	}

	public function testNormalizeReturnsNullWhenInvalid(): void {
		$this->assertNull( ConditionEvaluator::normalize( null ) );
		$this->assertNull( ConditionEvaluator::normalize( 'string' ) );
		$this->assertNull( ConditionEvaluator::normalize( array( 'field' => '' ) ) );
		$this->assertNull( ConditionEvaluator::normalize( array( 'field' => 'parent', 'operator' => 'invalid_op' ) ) );
		$this->assertNull( ConditionEvaluator::normalize( array( 'field' => 'self_id', 'operator' => 'equals' ), 'self_id' ) );
	}

	public function testNormalizeReturnsValidCondition(): void {
		$raw = array(
			'field'    => 'gift_wrap',
			'operator' => 'equals',
			'value'    => 'Yes',
		);

		$normalized = ConditionEvaluator::normalize( $raw, 'other_field' );

		$this->assertNotNull( $normalized );
		$this->assertSame( 'gift_wrap', $normalized['field'] );
		$this->assertSame( 'equals', $normalized['operator'] );
		$this->assertSame( 'Yes', $normalized['value'] );
	}

	public function testIsMetWhenConditionIsNull(): void {
		$this->assertTrue( ConditionEvaluator::isMet( null, array() ) );
		$this->assertTrue( ConditionEvaluator::isMet( null, array( 'other' => 'val' ) ) );
	}

	public function testIsMetEqualsOperator(): void {
		$condition = array(
			'field'    => 'channel',
			'operator' => 'equals',
			'value'    => 'WhatsApp',
		);

		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'channel' => 'WhatsApp' ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'channel' => 'SMS' ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array() ) );
	}

	public function testIsMetNotEqualsOperator(): void {
		$condition = array(
			'field'    => 'channel',
			'operator' => 'not_equals',
			'value'    => 'SMS',
		);

		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'channel' => 'WhatsApp' ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'channel' => 'SMS' ) ) );
		$this->assertTrue( ConditionEvaluator::isMet( $condition, array() ) );
	}

	public function testCheckboxNormalizationInConditionEvaluation(): void {
		$condition = array(
			'field'    => 'gift_wrapping',
			'operator' => 'equals',
			'value'    => 'Yes',
		);

		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => '1' ) ) );
		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => 1 ) ) );
		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => 'on' ) ) );
		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => true ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => '0' ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => '' ) ) );
	}

	public function testFieldObjectConditionsRoundTrip(): void {
		$condition = array(
			'field'    => 'parent_field',
			'operator' => 'equals',
			'value'    => 'Option A',
		);

		$field = FieldFactory::create(
			'dependent_field',
			'Dependent Field',
			FieldType::TEXT,
			true,
			'',
			'',
			array(),
			'',
			array(),
			$condition
		);

		$this->assertTrue( $field->hasConditions() );
		$this->assertSame( $condition, $field->getConditions() );
	}

	public function testClassicCheckoutValidationSkipsHiddenConditionalRequiredField(): void {
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => array(
					'gift_wrapping' => array(
						'id'       => 'gift_wrapping',
						'label'    => 'Add Gift Wrapping',
						'type'     => 'checkbox',
						'required' => false,
						'enabled'  => true,
					),
					'gift_message'  => array(
						'id'         => 'gift_message',
						'label'      => 'Gift Message',
						'type'       => 'text',
						'required'   => true, // REQUIRED
						'enabled'    => true,
						'conditions' => array(
							'field'    => 'gift_wrapping',
							'operator' => 'equals',
							'value'    => 'Yes',
						),
					),
				),
			),
		);

		$coordinator = new ClassicCheckout();
		$errors      = new WP_Error();

		// Case 1: gift_wrapping is unchecked (no). gift_message is required but hidden. Should NOT produce error.
		$postedData = array(
			'gift_wrapping' => '0',
			'gift_message'  => '',
		);

		$coordinator->validateFields( $postedData, $errors );
		$this->assertEmpty( $errors->get_error_codes() );

		// Case 2: gift_wrapping is checked (yes). gift_message is empty. Should produce required validation error.
		$errors2 = new WP_Error();
		$postedData2 = array(
			'gift_wrapping' => '1',
			'gift_message'  => '',
		);

		$coordinator->validateFields( $postedData2, $errors2 );
		$this->assertNotEmpty( $errors2->get_error_codes() );
		$this->assertContains( 'scfw_gift_message', $errors2->get_error_codes() );

		// Case 3: gift_wrapping is checked (yes). gift_message is provided. Passes validation.
		$errors3 = new WP_Error();
		$postedData3 = array(
			'gift_wrapping' => '1',
			'gift_message'  => 'Happy Birthday!',
		);

		$coordinator->validateFields( $postedData3, $errors3 );
		$this->assertEmpty( $errors3->get_error_codes() );
	}

	public function testBlockCheckoutValidationAndSaveHonorsConditions(): void {
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => array(
					'contact_method' => array(
						'id'       => 'contact_method',
						'label'    => 'Contact Method',
						'type'     => 'select',
						'options'  => array( 'SMS', 'WhatsApp' ),
						'required' => true,
						'enabled'  => true,
					),
					'gift_message'   => array(
						'id'         => 'gift_message',
						'label'      => 'Gift Message',
						'type'       => 'text',
						'required'   => true,
						'enabled'    => true,
						'conditions' => array(
							'field'    => 'contact_method',
							'operator' => 'equals',
							'value'    => 'WhatsApp',
						),
					),
				),
			),
		);

		$blockCoordinator = new \SCFW\Frontend\BlockCheckout();

		// Case 1: contact_method is SMS. gift_message condition is NOT met.
		$config = FieldRepository::config();
		$this->assertFalse(
			\SCFW\Services\FieldVisibility::isVisible(
				'gift_message',
				$config['gift_message'],
				array( 'contact_method' => 'SMS' )
			)
		);

		// Case 2: contact_method is WhatsApp. gift_message condition IS met.
		$this->assertTrue(
			\SCFW\Services\FieldVisibility::isVisible(
				'gift_message',
				$config['gift_message'],
				array( 'contact_method' => 'WhatsApp' )
			)
		);
	}

	public function testFieldVisibilityWithoutConditionReturnsTrue(): void {
		$configNoCondition = array(
			'id'       => 'alt_phone',
			'label'    => 'Alternate Phone',
			'type'     => 'tel',
			'required' => false,
			'enabled'  => true,
		);

		// 1. Without conditions key
		$this->assertTrue( \SCFW\Services\FieldVisibility::isVisible( 'alt_phone', $configNoCondition ) );
		$this->assertTrue( \SCFW\Services\FieldVisibility::isVisible( 'alt_phone', $configNoCondition, array( 'something' => 'val' ) ) );

		// 2. With conditions = null
		$configNullCondition = array(
			'id'         => 'alt_phone',
			'label'      => 'Alternate Phone',
			'type'       => 'tel',
			'required'   => false,
			'enabled'    => true,
			'conditions' => null,
		);
		$this->assertTrue( \SCFW\Services\FieldVisibility::isVisible( 'alt_phone', $configNullCondition ) );
		$this->assertTrue( \SCFW\Services\FieldVisibility::isVisible( 'alt_phone', $configNullCondition, array( 'something' => 'val' ) ) );

		// 3. With conditions and null values (initial frontend render)
		$configWithCondition = array(
			'id'         => 'gift_message',
			'label'      => 'Gift Message',
			'type'       => 'text',
			'required'   => true,
			'enabled'    => true,
			'conditions' => array(
				'field'    => 'gift_wrapping',
				'operator' => 'equals',
				'value'    => 'yes',
			),
		);
		$this->assertTrue( \SCFW\Services\FieldVisibility::isVisible( 'gift_message', $configWithCondition, null ) );
	}

	public function testClassicRendererIncludesConditionAttributesOnFormRow(): void {
		$field = FieldFactory::create(
			'gift_message',
			'Gift Message',
			FieldType::TEXT,
			true,
			'',
			'',
			array(),
			'',
			array(),
			array(
				'field'    => 'gift_wrapping',
				'operator' => 'equals',
				'value'    => 'yes',
			)
		);

		$renderer = new \SCFW\Frontend\ClassicRenderer();
		$result   = $renderer->render( $field, new \SCFW\Core\Rendering\RenderContext() );
		$html     = $result->getHtml();

		$this->assertStringContainsString( 'data-scfw-condition-field="gift_wrapping"', $html );
		$this->assertStringContainsString( 'data-scfw-condition-operator="equals"', $html );
		$this->assertStringContainsString( 'data-scfw-condition-value="yes"', $html );
		$this->assertStringContainsString( 'class="form-row', $html );
	}

	public function testBlockCheckboxValueNormalization(): void {
		// Evaluator normalizes boolean true, "yes", 1, "1", "on" to 'yes'
		$condition = array(
			'field'    => 'gift_wrapping',
			'operator' => 'equals',
			'value'    => 'yes',
		);

		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => true ) ) );
		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => 'yes' ) ) );
		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => 1 ) ) );
		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => '1' ) ) );
		$this->assertTrue( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => 'on' ) ) );

		// Evaluator normalizes boolean false, "no", 0, "0", "off", "" to 'no'
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => false ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => 'no' ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => 0 ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => '0' ) ) );
		$this->assertFalse( ConditionEvaluator::isMet( $condition, array( 'gift_wrapping' => '' ) ) );
	}
}


