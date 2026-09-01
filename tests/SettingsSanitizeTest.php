<?php
/**
 * Settings sanitize tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Admin\Settings;

/**
 * Verifies that an ordinary settings save preserves Free extra properties and
 * opaque Pro rules that the builder does not submit yet, and that a type
 * conversion clears only the properties that the new type cannot carry.
 */
final class SettingsSanitizeTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['scfw_test_options'] = array();
	}

	/**
	 * Build a stored canonical field configuration entry.
	 *
	 * @param array<string, mixed> $extra Overrides.
	 * @return array<string, mixed> Field entry.
	 */
	private function storedEntry( array $extra = array() ): array {
		return array_merge(
			array(
				'id'          => 'size',
				'label'       => 'Size',
				'type'        => 'select',
				'required'    => false,
				'enabled'     => true,
				'placeholder' => 'Pick one',
				'description' => 'Choose a size',
				'default'     => 'M',
				'options'     => array( 'S', 'M', 'L' ),
				'rules'       => array( array( 'field' => 'colour', 'op' => '==', 'value' => 'red' ) ),
			),
			$extra
		);
	}

	/**
	 * Extract the sanitized fields list.
	 *
	 * @param array<string, mixed> $clean Sanitized settings.
	 * @return array<string, array<string, mixed>> Canonical fields keyed by id.
	 */
	private function savedFields( array $clean ): array {
		/** @var array<string, array<string, mixed>> $fields */
		$fields = $clean['fields'] ?? array();

		return is_array( $fields ) ? $fields : array();
	}

	public function testSavePreservesBuilderMissingProperties(): void {
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => array( 'size' => $this->storedEntry() ),
			),
		);

		$clean  = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'       => 'size',
						'label'    => 'Size',
						'type'     => 'select',
						'required' => '0',
						'enabled'  => '1',
					),
				),
			)
		);
		$fields = $clean['fields'] ?? array();
		$size   = $fields['size'] ?? array();

		$this->assertIsArray( $fields );
		$this->assertIsArray( $size );
		$this->assertSame( 'Pick one', $size['placeholder'] ?? null );
		$this->assertSame( 'Choose a size', $size['description'] ?? null );
		$this->assertSame( 'M', $size['default'] ?? null );
		$this->assertSame( array( 'S', 'M', 'L' ), $size['options'] ?? null );
		$this->assertSame(
			array( array( 'field' => 'colour', 'op' => '==', 'value' => 'red' ) ),
			$size['rules'] ?? null
		);
	}

	public function testSaveDropsStoredPropertiesForNewId(): void {
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => array( 'size' => $this->storedEntry() ),
			),
		);

		$clean  = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'       => 'renamed',
						'label'    => 'Size',
						'type'     => 'select',
						'required' => '0',
						'enabled'  => '1',
					),
				),
			)
		);
		$fields = $clean['fields'] ?? array();
		$renamed = $fields['renamed'] ?? array();

		$this->assertIsArray( $fields );
		$this->assertIsArray( $renamed );
		$this->assertSame( '', $renamed['placeholder'] ?? null );
		$this->assertSame( '', $renamed['default'] ?? null );
		$this->assertSame( array(), $renamed['options'] ?? null );
	}

	public function testSaveRejectsInvalidStoredChoiceConfig(): void {
		$stored        = array( 'size' => $this->storedEntry( array( 'options' => array() ) ) );
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => $stored,
			),
		);

		$clean  = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'       => 'size',
						'label'    => 'Size',
						'type'     => 'select',
						'required' => '0',
						'enabled'  => '1',
					),
				),
			)
		);

		$this->assertSame( $stored, $clean['fields'] ?? null );
	}

	public function testSelectToCheckboxConversionClearsChoiceOnlyProperties(): void {
		$stored        = array( 'size' => $this->storedEntry() );
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => $stored,
			),
		);

		$clean  = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'       => 'size',
						'label'    => 'Size',
						'type'     => 'checkbox',
						'required' => '0',
						'enabled'  => '1',
					),
				),
			)
		);
		$size = $this->savedFields( $clean )['size'] ?? array();

		$this->assertIsArray( $size );
		$this->assertSame( 'checkbox', $size['type'] ?? null );
		$this->assertSame( array(), $size['options'] ?? null );
		$this->assertSame( '', $size['default'] ?? null );
		$this->assertSame( 'Pick one', $size['placeholder'] ?? null );
		$this->assertSame( 'Choose a size', $size['description'] ?? null );
		$this->assertSame(
			array( array( 'field' => 'colour', 'op' => '==', 'value' => 'red' ) ),
			$size['rules'] ?? null
		);
	}

	public function testRadioToCheckboxConversionClearsChoiceDefault(): void {
		$stored        = array( 'pick' => $this->storedEntry( array( 'type' => 'radio', 'default' => 'M' ) ) );
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => $stored,
			),
		);

		$clean  = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'       => 'pick',
						'label'    => 'Pick',
						'type'     => 'checkbox',
						'required' => '0',
						'enabled'  => '1',
					),
				),
			)
		);
		$pick = $this->savedFields( $clean )['pick'] ?? array();

		$this->assertIsArray( $pick );
		$this->assertSame( 'checkbox', $pick['type'] ?? null );
		$this->assertSame( '', $pick['default'] ?? null );
	}

	public function testCheckboxToSelectConversionClearsCapturedDefault(): void {
		$stored        = array(
			'tou' => $this->storedEntry(
				array(
					'type'        => 'checkbox',
					'placeholder' => '',
					'description' => '',
					'default'     => '1',
					'options'     => array(),
					'rules'       => array(),
				)
			),
		);
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => $stored,
			),
		);

		$clean  = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'       => 'tou',
						'label'    => 'Opt-in',
						'type'     => 'select',
						'required' => '0',
						'enabled'  => '1',
						'options'  => array( 'A', 'B' ),
					),
				),
			)
		);
		$tou = $this->savedFields( $clean )['tou'] ?? array();

		$this->assertIsArray( $tou );
		$this->assertSame( 'select', $tou['type'] ?? null );
		$this->assertSame( array( 'A', 'B' ), $tou['options'] ?? null );
		$this->assertSame( '', $tou['default'] ?? null );
	}

	public function testCheckboxToSelectConversionWithoutOptionsIsValid(): void {
		$stored        = array(
			'tou' => $this->storedEntry(
				array(
					'type'        => 'checkbox',
					'placeholder' => '',
					'description' => '',
					'default'     => '1',
					'options'     => array(),
					'rules'       => array(),
				)
			),
		);
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => $stored,
			),
		);

		$clean  = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'       => 'tou',
						'label'    => 'Opt-in',
						'type'     => 'select',
						'required' => '0',
						'enabled'  => '1',
					),
				),
			)
		);
		$tou = $this->savedFields( $clean )['tou'] ?? array();

		$this->assertIsArray( $tou );
		$this->assertSame( 'select', $tou['type'] ?? null );
		$this->assertSame( array(), $tou['options'] ?? null );
		$this->assertSame( '', $tou['default'] ?? null );
	}

	public function testTextToRadioConversionClearsTextDefault(): void {
		$stored        = array( 'note' => $this->storedEntry( array( 'type' => 'text', 'default' => 'Hello' ) ) );
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => $stored,
			),
		);

		$clean  = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'       => 'note',
						'label'    => 'Note',
						'type'     => 'radio',
						'required' => '0',
						'enabled'  => '1',
						'options'  => array( 'Y', 'N' ),
					),
				),
			)
		);
		$note = $this->savedFields( $clean )['note'] ?? array();

		$this->assertIsArray( $note );
		$this->assertSame( 'radio', $note['type'] ?? null );
		$this->assertSame( array( 'Y', 'N' ), $note['options'] ?? null );
		$this->assertSame( '', $note['default'] ?? null );
	}

	public function testSavePreservesAndUpdatesDestinationsAndConditions(): void {
		$stored = array(
			'gift_wrap' => $this->storedEntry(
				array(
					'id'           => 'gift_wrap',
					'label'        => 'Gift Wrap',
					'type'         => 'checkbox',
					'destinations' => array( 'admin_order', 'customer_email' ),
					'conditions'   => null,
				)
			),
			'gift_msg'  => $this->storedEntry(
				array(
					'id'           => 'gift_msg',
					'label'        => 'Gift Message',
					'type'         => 'text',
					'destinations' => array( 'admin_order', 'thank_you' ),
					'conditions'   => array(
						'field'    => 'gift_wrap',
						'operator' => 'equals',
						'value'    => 'Yes',
					),
				)
			),
		);

		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => $stored,
			),
		);

		// Submitting an update with changed label and new destination selection for gift_wrap.
		$clean = Settings::sanitize(
			array(
				'enabled' => '1',
				'fields'  => array(
					array(
						'id'           => 'gift_wrap',
						'label'        => 'Add Gift Wrapping?',
						'type'         => 'checkbox',
						'required'     => '0',
						'enabled'      => '1',
						'destinations' => array( 'admin_order', 'thank_you', 'my_account' ),
					),
					array(
						'id'           => 'gift_msg',
						'label'        => 'Gift Message',
						'type'         => 'text',
						'required'     => '1',
						'enabled'      => '1',
						'destinations' => array( 'admin_order' ),
						'conditions'   => array(
							'field'    => 'gift_wrap',
							'operator' => 'equals',
							'value'    => 'Yes',
						),
					),
				),
			)
		);

		$fields = $this->savedFields( $clean );
		$this->assertCount( 2, $fields );
		$this->assertSame( 'Add Gift Wrapping?', $fields['gift_wrap']['label'] );
		$this->assertSame( array( 'admin_order', 'thank_you', 'my_account' ), $fields['gift_wrap']['destinations'] );
		$this->assertNull( $fields['gift_wrap']['conditions'] );

		$this->assertSame( array( 'admin_order' ), $fields['gift_msg']['destinations'] );
		$this->assertNotNull( $fields['gift_msg']['conditions'] );
		$this->assertSame( 'gift_wrap', $fields['gift_msg']['conditions']['field'] );
		$this->assertSame( 'equals', $fields['gift_msg']['conditions']['operator'] );
		$this->assertSame( 'Yes', $fields['gift_msg']['conditions']['value'] );
	}
}