<?php
/**
 * Developer Diagnostics tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Admin\DeveloperDiagnostics;
use SCFW\Admin\Settings;
use SCFW\Core\Field\FieldDestination;

/**
 * Tests for Developer Diagnostics settings, meta key calculation, lifecycle presentation,
 * and condition summaries.
 */
final class DeveloperDiagnosticsTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['scfw_test_options'] = array();
	}

	public function testDeveloperDiagnosticsDefaultsToOff(): void {
		$this->assertFalse( Settings::isDeveloperDiagnosticsEnabled() );
	}

	public function testDeveloperDiagnosticsCanBeEnabled(): void {
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled'               => 1,
				'developer_diagnostics' => 1,
			),
		);

		$this->assertTrue( Settings::isDeveloperDiagnosticsEnabled() );
	}

	public function testSettingsSanitizePreservesAndUpdatesDeveloperDiagnostics(): void {
		$clean = Settings::sanitize(
			array(
				'enabled'               => '1',
				'developer_diagnostics' => '1',
			)
		);

		$this->assertSame( 1, $clean['developer_diagnostics'] );
		$this->assertSame( 1, $clean['enabled'] );

		// Turn off
		$cleanOff = Settings::sanitize(
			array(
				'enabled'               => '1',
				'developer_diagnostics' => '0',
			)
		);

		$this->assertSame( 0, $cleanOff['developer_diagnostics'] );
	}

	public function testMetaKeyGeneration(): void {
		$this->assertSame( '_scfw_gift_message', DeveloperDiagnostics::metaKey( 'gift_message' ) );
		$this->assertSame( '_scfw_alt_phone', DeveloperDiagnostics::metaKey( 'alt_phone' ) );
		$this->assertSame( '_scfw_delivery-notes', DeveloperDiagnostics::metaKey( 'delivery-notes' ) );
	}

	public function testDestinationLabelsAreComplete(): void {
		$labels = DeveloperDiagnostics::destinationLabels();

		$this->assertArrayHasKey( FieldDestination::ADMIN_ORDER->value, $labels );
		$this->assertArrayHasKey( FieldDestination::THANK_YOU->value, $labels );
		$this->assertArrayHasKey( FieldDestination::MY_ACCOUNT->value, $labels );
		$this->assertArrayHasKey( FieldDestination::CUSTOMER_EMAIL->value, $labels );
		$this->assertArrayHasKey( FieldDestination::ADMIN_EMAIL->value, $labels );
	}

	public function testIsDestinationActive(): void {
		$fieldWithAll = array(
			'id'           => 'notes',
			'destinations' => FieldDestination::all(),
		);

		$this->assertTrue( DeveloperDiagnostics::isDestinationActive( $fieldWithAll, FieldDestination::ADMIN_ORDER->value ) );
		$this->assertTrue( DeveloperDiagnostics::isDestinationActive( $fieldWithAll, FieldDestination::THANK_YOU->value ) );

		$fieldWithSubset = array(
			'id'           => 'custom_ref',
			'destinations' => array( FieldDestination::ADMIN_ORDER->value, FieldDestination::ADMIN_EMAIL->value ),
		);

		$this->assertTrue( DeveloperDiagnostics::isDestinationActive( $fieldWithSubset, FieldDestination::ADMIN_ORDER->value ) );
		$this->assertFalse( DeveloperDiagnostics::isDestinationActive( $fieldWithSubset, FieldDestination::THANK_YOU->value ) );
		$this->assertFalse( DeveloperDiagnostics::isDestinationActive( $fieldWithSubset, FieldDestination::CUSTOMER_EMAIL->value ) );
		$this->assertTrue( DeveloperDiagnostics::isDestinationActive( $fieldWithSubset, FieldDestination::ADMIN_EMAIL->value ) );
	}

	public function testConditionSummaryFormatting(): void {
		$unconditionalField = array(
			'id'         => 'gift_wrap',
			'conditions' => null,
		);
		$this->assertSame( 'None', DeveloperDiagnostics::conditionSummary( $unconditionalField ) );

		$conditionalField = array(
			'id'         => 'gift_message',
			'conditions' => array(
				'field'    => 'gift_wrap',
				'operator' => 'equals',
				'value'    => 'yes',
			),
		);
		$this->assertSame( 'gift_wrap equals yes', DeveloperDiagnostics::conditionSummary( $conditionalField ) );
	}
}
