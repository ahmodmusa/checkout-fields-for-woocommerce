<?php
/**
 * FieldDestination and presentation filtering tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Core\Field\Field;
use SCFW\Core\Field\FieldDestination;
use SCFW\Core\Field\FieldFactory;
use SCFW\Core\Field\FieldRepository;
use SCFW\Core\Field\FieldType;
use SCFW\Services\OrderFieldPresentation;
use WC_Order;

/**
 * Verifies FieldDestination normalization and OrderFieldPresentation filtering across touchpoints.
 */
final class FieldDestinationTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['scfw_test_options'] = array();
	}

	public function testFieldDestinationAllReturnsAllTouchpoints(): void {
		$all = FieldDestination::all();

		$this->assertSame(
			array(
				'admin_order',
				'thank_you',
				'my_account',
				'customer_email',
				'admin_email',
			),
			$all
		);
	}

	public function testNormalizeReturnsAllWhenNullOrNonArray(): void {
		$this->assertSame( FieldDestination::all(), FieldDestination::normalize( null ) );
		$this->assertSame( FieldDestination::all(), FieldDestination::normalize( 'invalid' ) );
	}

	public function testNormalizeFiltersInvalidAndDuplicateValues(): void {
		$raw = array( 'admin_order', 'invalid_touchpoint', 'admin_order', 'thank_you' );
		$normalized = FieldDestination::normalize( $raw );

		$this->assertSame( array( 'admin_order', 'thank_you' ), $normalized );
	}

	public function testFieldDefaultsToAllDestinationsWhenNoneProvided(): void {
		$field = FieldFactory::create( 'notes', 'Special Notes', FieldType::TEXT );

		$this->assertSame( FieldDestination::all(), $field->getDestinations() );
		$this->assertTrue( $field->hasDestination( FieldDestination::ADMIN_ORDER ) );
		$this->assertTrue( $field->hasDestination( FieldDestination::THANK_YOU ) );
		$this->assertTrue( $field->hasDestination( FieldDestination::MY_ACCOUNT ) );
		$this->assertTrue( $field->hasDestination( FieldDestination::CUSTOMER_EMAIL ) );
		$this->assertTrue( $field->hasDestination( FieldDestination::ADMIN_EMAIL ) );
	}

	public function testFieldAcceptsSpecificDestinations(): void {
		$field = FieldFactory::create(
			'internal_note',
			'Internal Note',
			FieldType::TEXT,
			false,
			'',
			'',
			array(),
			'',
			array( 'admin_order', 'admin_email' )
		);

		$this->assertSame( array( 'admin_order', 'admin_email' ), $field->getDestinations() );
		$this->assertTrue( $field->hasDestination( FieldDestination::ADMIN_ORDER ) );
		$this->assertTrue( $field->hasDestination( FieldDestination::ADMIN_EMAIL ) );
		$this->assertFalse( $field->hasDestination( FieldDestination::THANK_YOU ) );
		$this->assertFalse( $field->hasDestination( FieldDestination::MY_ACCOUNT ) );
		$this->assertFalse( $field->hasDestination( FieldDestination::CUSTOMER_EMAIL ) );
	}

	public function testFieldRepositoryNormalizesMissingDestinationsToAll(): void {
		$entry = array(
			'id'       => 'test_field',
			'label'    => 'Test Field',
			'type'     => 'text',
			'required' => false,
			'enabled'  => true,
		);

		$normalized = FieldRepository::normalizeFieldEntry( $entry );

		$this->assertNotNull( $normalized );
		$this->assertSame( FieldDestination::all(), $normalized['destinations'] );
	}

	public function testOrderFieldPresentationFiltersByDestination(): void {
		// Mock options with two fields with different destinations.
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => array(
					'public_field' => array(
						'id'           => 'public_field',
						'label'        => 'Public Field',
						'type'         => 'text',
						'required'     => false,
						'enabled'      => true,
						'destinations' => array( 'thank_you', 'my_account', 'customer_email' ),
					),
					'internal_field' => array(
						'id'           => 'internal_field',
						'label'        => 'Internal Note',
						'type'         => 'text',
						'required'     => false,
						'enabled'      => true,
						'destinations' => array( 'admin_order', 'admin_email' ),
					),
				),
			),
		);

		$order = $this->createMock( WC_Order::class );
		$order->method( 'get_meta' )->willReturnCallback(
			function ( string $key ) {
				if ( '_scfw_public_field' === $key ) {
					return 'Customer Value';
				}
				if ( '_scfw_internal_field' === $key ) {
					return 'Admin Only Value';
				}
				return '';
			}
		);

		// 1. All rows when destination is null.
		$allRows = OrderFieldPresentation::rowsFor( $order );
		$this->assertCount( 2, $allRows );

		// 2. Admin Order destination only gets internal_field.
		$adminRows = OrderFieldPresentation::rowsFor( $order, FieldDestination::ADMIN_ORDER );
		$this->assertCount( 1, $adminRows );
		$this->assertSame( 'internal_field', $adminRows[0]['id'] );
		$this->assertSame( 'Admin Only Value', $adminRows[0]['value'] );

		// 3. Thank You destination only gets public_field.
		$thankYouRows = OrderFieldPresentation::rowsFor( $order, FieldDestination::THANK_YOU );
		$this->assertCount( 1, $thankYouRows );
		$this->assertSame( 'public_field', $thankYouRows[0]['id'] );
		$this->assertSame( 'Customer Value', $thankYouRows[0]['value'] );

		// 4. My Account destination only gets public_field.
		$myAccountRows = OrderFieldPresentation::rowsFor( $order, FieldDestination::MY_ACCOUNT );
		$this->assertCount( 1, $myAccountRows );
		$this->assertSame( 'public_field', $myAccountRows[0]['id'] );

		// 5. Customer Email gets public_field.
		$custEmailRows = OrderFieldPresentation::rowsFor( $order, FieldDestination::CUSTOMER_EMAIL );
		$this->assertCount( 1, $custEmailRows );
		$this->assertSame( 'public_field', $custEmailRows[0]['id'] );

		// 6. Admin Email gets internal_field.
		$adminEmailRows = OrderFieldPresentation::rowsFor( $order, FieldDestination::ADMIN_EMAIL );
		$this->assertCount( 1, $adminEmailRows );
		$this->assertSame( 'internal_field', $adminEmailRows[0]['id'] );
	}
}
