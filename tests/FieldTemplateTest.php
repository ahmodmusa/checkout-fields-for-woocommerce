<?php
/**
 * Field Template Test Suite.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Core\Field\FieldDestination;
use SCFW\Core\Field\FieldRepository;
use SCFW\Core\Field\FieldTemplateRepository;
use SCFW\Core\Field\FieldType;

/**
 * Tests for FieldTemplateRepository and duplicate-safe application.
 */
final class FieldTemplateTest extends TestCase {

	/**
	 * Test that all 4 expected template packs exist.
	 */
	public function testAllFourTemplatesExist(): void {
		$all = FieldTemplateRepository::all();

		$this->assertCount( 4, $all );
		$this->assertArrayHasKey( FieldTemplateRepository::LOCAL_DELIVERY, $all );
		$this->assertArrayHasKey( FieldTemplateRepository::GIFTING, $all );
		$this->assertArrayHasKey( FieldTemplateRepository::B2B_WHOLESALE, $all );
		$this->assertArrayHasKey( FieldTemplateRepository::DIRECT_CONTACT, $all );
	}

	/**
	 * Test that each template has valid metadata and non-empty fields.
	 */
	public function testTemplateMetadataIsValid(): void {
		$all = FieldTemplateRepository::all();

		foreach ( $all as $id => $template ) {
			$this->assertSame( $id, $template['id'] );
			$this->assertNotEmpty( $template['title'] );
			$this->assertNotEmpty( $template['description'] );
			$this->assertIsArray( $template['fields'] );
			$this->assertNotEmpty( $template['fields'] );

			// Check that each field is fully valid under strict normalization
			foreach ( $template['fields'] as $field_def ) {
				$normalized = FieldRepository::normalizeFieldEntry( $field_def, true );
				$this->assertNotNull( $normalized, "Field {$field_def['id']} in template {$id} must normalize strictly." );
				$this->assertSame( $field_def['id'], $normalized['id'] );
				$this->assertSame( $field_def['type'], $normalized['type'] );
				$this->assertNotEmpty( $normalized['destinations'] );
			}
		}
	}

	/**
	 * Test that Gifting template includes conditional relationship for Gift Message.
	 */
	public function testGiftingContainsConditionalRelationship(): void {
		$gifting = FieldTemplateRepository::get( FieldTemplateRepository::GIFTING );
		$this->assertNotNull( $gifting );

		$field_map = array();
		foreach ( $gifting['fields'] as $f ) {
			$field_map[ $f['id'] ] = $f;
		}

		$this->assertArrayHasKey( 'gift_wrapping', $field_map );
		$this->assertArrayHasKey( 'gift_message', $field_map );
		$this->assertArrayHasKey( 'recipient_name', $field_map );

		$this->assertSame( FieldType::CHECKBOX->value, $field_map['gift_wrapping']['type'] );
		$this->assertSame( FieldType::TEXTAREA->value, $field_map['gift_message']['type'] );
		$this->assertSame( FieldType::TEXT->value, $field_map['recipient_name']['type'] );

		// Conditions check
		$conditions = $field_map['gift_message']['conditions'];
		$this->assertIsArray( $conditions );
		$this->assertSame( 'gift_wrapping', $conditions['field'] );
		$this->assertSame( 'equals', $conditions['operator'] );
		$this->assertSame( 'yes', $conditions['value'] );
	}

	/**
	 * Test that generating fields with no existing collisions preserves original IDs.
	 */
	public function testGenerateFieldsWithNoCollisions(): void {
		$fields = FieldTemplateRepository::generateFields( FieldTemplateRepository::LOCAL_DELIVERY, array() );

		$this->assertCount( 3, $fields );
		$this->assertSame( 'preferred_date', $fields[0]['id'] );
		$this->assertSame( 'delivery_time_slot', $fields[1]['id'] );
		$this->assertSame( 'delivery_instructions', $fields[2]['id'] );
	}

	/**
	 * Test duplicate ID resolution and generic condition reference rewriting.
	 */
	public function testDuplicateIdResolutionAndConditionRewriting(): void {
		// Existing fields that collide with 'gift_wrapping' and 'gift_message'
		$existing = array(
			'gift_wrapping' => array( 'id' => 'gift_wrapping', 'label' => 'Existing Gift Wrap' ),
			'gift_message'  => array( 'id' => 'gift_message', 'label' => 'Existing Gift Message' ),
		);

		$generated = FieldTemplateRepository::generateFields( FieldTemplateRepository::GIFTING, $existing );

		$this->assertCount( 3, $generated );

		$field_map = array();
		foreach ( $generated as $f ) {
			$field_map[ $f['id'] ] = $f;
		}

		// The IDs should have received unique suffixes
		$this->assertArrayNotHasKey( 'gift_wrapping', $field_map );
		$this->assertArrayHasKey( 'gift_wrapping_2', $field_map );
		$this->assertArrayNotHasKey( 'gift_message', $field_map );
		$this->assertArrayHasKey( 'gift_message_2', $field_map );
		$this->assertArrayHasKey( 'recipient_name', $field_map );

		// The condition on gift_message_2 must point to gift_wrapping_2, NOT the old gift_wrapping
		$conditions = $field_map['gift_message_2']['conditions'];
		$this->assertIsArray( $conditions );
		$this->assertSame( 'gift_wrapping_2', $conditions['field'] );
		$this->assertSame( 'equals', $conditions['operator'] );
		$this->assertSame( 'yes', $conditions['value'] );
	}

	/**
	 * Test applying a template pack repeatedly is safe and generates incremental unique IDs.
	 */
	public function testRepeatedApplicationIsSafe(): void {
		$first_run = FieldTemplateRepository::generateFields( FieldTemplateRepository::DIRECT_CONTACT, array() );
		$this->assertSame( 'alt_phone', $first_run[0]['id'] );
		$this->assertSame( 'contact_channel', $first_run[1]['id'] );

		// Simulate existing fields now having first_run
		$existing = array();
		foreach ( $first_run as $f ) {
			$existing[ $f['id'] ] = $f;
		}

		$second_run = FieldTemplateRepository::generateFields( FieldTemplateRepository::DIRECT_CONTACT, $existing );
		$this->assertSame( 'alt_phone_2', $second_run[0]['id'] );
		$this->assertSame( 'contact_channel_2', $second_run[1]['id'] );

		// Merge second run into existing
		foreach ( $second_run as $f ) {
			$existing[ $f['id'] ] = $f;
		}

		$third_run = FieldTemplateRepository::generateFields( FieldTemplateRepository::DIRECT_CONTACT, $existing );
		$this->assertSame( 'alt_phone_3', $third_run[0]['id'] );
		$this->assertSame( 'contact_channel_3', $third_run[1]['id'] );
	}

	/**
	 * Test applying non-existent template returns empty array and false on apply.
	 */
	public function testNonExistentTemplate(): void {
		$this->assertNull( FieldTemplateRepository::get( 'non_existent' ) );
		$this->assertEmpty( FieldTemplateRepository::generateFields( 'non_existent', array() ) );
		$this->assertFalse( FieldTemplateRepository::applyTemplate( 'non_existent' ) );
	}

	/**
	 * Test destination defaults are complete across all template fields.
	 */
	public function testAllTemplateFieldsHaveAllDestinationsByDefault(): void {
		$all = FieldTemplateRepository::all();
		$all_destinations = FieldDestination::all();

		foreach ( $all as $pack ) {
			foreach ( $pack['fields'] as $f ) {
				$this->assertEquals( $all_destinations, $f['destinations'] );
			}
		}
	}
}
