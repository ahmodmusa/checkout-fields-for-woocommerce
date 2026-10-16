<?php
/**
 * OrderDisplay and Order Admin editing tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Admin\OrderDisplay;
use SCFW\Core\Field\FieldDestination;
use SCFW\Core\Field\FieldFactory;
use SCFW\Core\Field\FieldType;
use SCFW\Services\OrderFieldPresentation;

/**
 * Tests for OrderDisplay rendering, editable fields extraction, and save handling.
 */
final class OrderDisplayTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['scfw_test_options']          = array();
		$GLOBALS['scfw_test_current_user_can'] = true;
		$GLOBALS['scfw_test_valid_nonce']      = true;
		$_POST                                 = array();
	}

	/**
	 * Create an in-memory WC_Order instance.
	 *
	 * @return \WC_Order
	 */
	private function createOrder(): \WC_Order {
		return new \WC_Order();
	}

	/**
	 * Setup a standard multi-type field configuration for testing.
	 */
	private function setupTestConfig(): void {
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => array(
					'customer_note' => array(
						'id'           => 'customer_note',
						'label'        => 'Customer Note',
						'type'         => 'text',
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
					'delivery_msg'  => array(
						'id'           => 'delivery_msg',
						'label'        => 'Delivery Instructions',
						'type'         => 'textarea',
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
					'contact_email' => array(
						'id'           => 'contact_email',
						'label'        => 'Contact Email',
						'type'         => 'email',
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
					'contact_phone' => array(
						'id'           => 'contact_phone',
						'label'        => 'Contact Phone',
						'type'         => 'tel',
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
					'item_count'    => array(
						'id'           => 'item_count',
						'label'        => 'Item Count',
						'type'         => 'number',
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
					'delivery_date' => array(
						'id'           => 'delivery_date',
						'label'        => 'Delivery Date',
						'type'         => 'date',
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
					'time_slot'     => array(
						'id'           => 'time_slot',
						'label'        => 'Time Slot',
						'type'         => 'select',
						'options'      => array( 'Morning', 'Evening' ),
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
					'channel_pref'  => array(
						'id'           => 'channel_pref',
						'label'        => 'Channel Preference',
						'type'         => 'radio',
						'options'      => array( 'SMS', 'WhatsApp' ),
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
					'is_gift'       => array(
						'id'           => 'is_gift',
						'label'        => 'Add Gift Wrap',
						'type'         => 'checkbox',
						'required'     => false,
						'enabled'      => true,
						'destinations' => FieldDestination::all(),
					),
				),
			),
		);
	}

	public function testEditableFieldsForOrder(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_scfw_customer_note', 'Please leave at front porch' );
		$order->update_meta_data( '_scfw_is_gift', '1' );

		$editable = OrderDisplay::editableFieldsFor( $order );

		$this->assertCount( 9, $editable );
		$this->assertSame( 'customer_note', $editable[0]['field']->getId() );
		$this->assertSame( 'Please leave at front porch', $editable[0]['raw_value'] );
		$this->assertSame( 'is_gift', $editable[8]['field']->getId() );
		$this->assertSame( '1', $editable[8]['raw_value'] );
	}

	public function testSaveTextValueUpdate(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'customer_note' => 'Ring doorbell twice <script>alert(1)</script>',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( 'Ring doorbell twice', $order->get_meta( '_scfw_customer_note' ) );
	}

	public function testSaveTextareaValueUpdate(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'delivery_msg' => "Line 1\nLine 2 <b>bold</b>",
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( "Line 1\nLine 2 bold", $order->get_meta( '_scfw_delivery_msg' ) );
	}

	public function testSaveEmailValueUpdate(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'contact_email' => 'customer@example.com',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( 'customer@example.com', $order->get_meta( '_scfw_contact_email' ) );
	}

	public function testSaveTelValueUpdate(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'contact_phone' => '+1 (555) 019-2834',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( '+1 (555) 019-2834', $order->get_meta( '_scfw_contact_phone' ) );
	}

	public function testSaveNumberValueUpdate(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'item_count' => '42',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( '42', $order->get_meta( '_scfw_item_count' ) );
	}

	public function testSaveDateValueUpdate(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'delivery_date' => '2026-12-25',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( '2026-12-25', $order->get_meta( '_scfw_delivery_date' ) );
	}

	public function testSaveSelectValidValue(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'time_slot' => 'Evening',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( 'Evening', $order->get_meta( '_scfw_time_slot' ) );
	}

	public function testSaveSelectInvalidValueRejected(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_scfw_time_slot', 'Morning' );

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'time_slot' => 'Invalid_Slot_Injection',
			),
		);

		OrderDisplay::save( 123, $order );

		// Retains original value because invalid option was rejected
		$this->assertSame( 'Morning', $order->get_meta( '_scfw_time_slot' ) );
	}

	public function testSaveRadioValidValue(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'channel_pref' => 'WhatsApp',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( 'WhatsApp', $order->get_meta( '_scfw_channel_pref' ) );
	}

	public function testSaveRadioInvalidValueRejected(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_scfw_channel_pref', 'SMS' );

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'channel_pref' => 'Pigeon',
			),
		);

		OrderDisplay::save( 123, $order );

		// Retains original value
		$this->assertSame( 'SMS', $order->get_meta( '_scfw_channel_pref' ) );
	}

	public function testSaveCheckboxChecked(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'is_gift' => '1',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( '1', $order->get_meta( '_scfw_is_gift' ) );
	}

	public function testSaveCheckboxUncheckedClearsMeta(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_scfw_is_gift', '1' );

		// When checkbox is unchecked, browsers omit it from POST
		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'customer_note' => 'Still here',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertNull( $order->get_meta( '_scfw_is_gift' ) );
	}

	public function testSaveEmptyValueClearsMeta(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_scfw_customer_note', 'Existing Note' );

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'customer_note' => '   ',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertNull( $order->get_meta( '_scfw_customer_note' ) );
	}

	public function testUnauthorizedUpdateBlocked(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_scfw_customer_note', 'Original Note' );

		$GLOBALS['scfw_test_current_user_can'] = false;

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'customer_note' => 'Hacked Note',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( 'Original Note', $order->get_meta( '_scfw_customer_note' ) );
	}

	public function testInvalidNonceBlocked(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_scfw_customer_note', 'Original Note' );

		$GLOBALS['scfw_test_valid_nonce'] = false;

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'bad_nonce',
			'scfw_fields'            => array(
				'customer_note' => 'Tampered Note',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( 'Original Note', $order->get_meta( '_scfw_customer_note' ) );
	}

	public function testUnrelatedOrderMetaPreserved(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_billing_custom_tax_id', 'TAX12345' );
		$order->update_meta_data( '_scfw_customer_note', 'Old' );

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'customer_note' => 'New Note',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertSame( 'New Note', $order->get_meta( '_scfw_customer_note' ) );
		$this->assertSame( 'TAX12345', $order->get_meta( '_billing_custom_tax_id' ) );
	}

	public function testUnknownOrDeletedFieldPostDataIgnored(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();

		$_POST = array(
			OrderDisplay::NONCE_NAME => 'valid_nonce',
			'scfw_fields'            => array(
				'arbitrary_injected_field' => 'Injected Value',
			),
		);

		OrderDisplay::save( 123, $order );

		$this->assertNull( $order->get_meta( '_scfw_arbitrary_injected_field' ) );
	}

	public function testOutputRendersViewAndEditContainers(): void {
		$this->setupTestConfig();
		$order = $this->createOrder();
		$order->update_meta_data( '_scfw_customer_note', 'Test Note' );

		ob_start();
		OrderDisplay::render( $order );
		$html = ob_get_clean();

		$this->assertStringContainsString( 'scfw-admin-order-fields', $html );
		$this->assertStringContainsString( 'scfw-edit-order-fields-btn', $html );
		$this->assertStringContainsString( 'scfw-cancel-order-fields-btn', $html );
		$this->assertStringContainsString( 'scfw-admin-order-fields-view', $html );
		$this->assertStringContainsString( 'scfw-admin-order-fields-edit', $html );
		$this->assertStringContainsString( 'Test Note', $html );
		$this->assertStringContainsString( 'name="scfw_fields[customer_note]"', $html );
		$this->assertStringContainsString( '_scfw_order_fields_nonce', $html );
	}
}
