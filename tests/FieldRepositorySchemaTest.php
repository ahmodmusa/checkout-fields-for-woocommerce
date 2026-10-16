<?php
/**
 * FieldRepository schema tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace SCFW\Tests;

use PHPUnit\Framework\TestCase;
use SCFW\Core\Field\FieldRepository;
use SCFW\Core\Field\FieldType;

/**
 * Verifies the Free field configuration schema normalization and the strict
 * atomic validation rules of the approved free field design baseline.
 */
final class FieldRepositorySchemaTest extends TestCase {

	protected function setUp(): void {
		$GLOBALS['scfw_test_options'] = array();
	}

	/**
	 * Build a well-formed field entry overridden by $extra.
	 *
	 * @param array<string, mixed> $extra Overrides.
	 * @param string               $type  Field type.
	 * @return array<string, mixed> Field entry.
	 */
	private function entry( array $extra = array(), string $type = 'text' ): array {
		return array_merge(
			array(
				'id'       => 'pickup',
				'label'    => 'Pickup Point',
				'type'     => $type,
				'required' => false,
				'enabled'  => true,
			),
			$extra
		);
	}

	public function testOldConfigurationNormalizesBackwardCompatibly(): void {
		$normalized = FieldRepository::normalizeFieldEntry( $this->entry() );

		$this->assertNotNull( $normalized );
		$this->assertSame( '', $normalized['placeholder'] );
		$this->assertSame( '', $normalized['description'] );
		$this->assertSame( '', $normalized['default'] );
		$this->assertSame( array(), $normalized['options'] );
		$this->assertSame( array(), $normalized['rules'] );
	}

	public function testOldConfigurationPassesStrictValidation(): void {
		$this->assertNotNull( FieldRepository::validateFields( array( $this->entry() ) ) );
	}

	public function testStrictModeRejectsEmptyLabel(): void {
		$this->assertNull( FieldRepository::normalizeFieldEntry( $this->entry( array( 'label' => '' ) ), true ) );
	}

	public function testPlaceholderAndDescriptionAreSanitized(): void {
		$normalized = FieldRepository::normalizeFieldEntry(
			$this->entry(
				array(
					'placeholder' => '  Enter here  ',
					'description' => "Line one\nline two",
				)
			)
		);

		$this->assertNotNull( $normalized );
		$this->assertSame( 'Enter here', $normalized['placeholder'] );
		$this->assertSame( 'Line one line two', $normalized['description'] );
	}

	public function testStrictModeRejectsNonScalarPlaceholder(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry( $this->entry( array( 'placeholder' => array( 'x' ) ) ), true )
		);
	}

	public function testChoiceFieldOptionsAndDefaultRoundTrip(): void {
		$normalized = FieldRepository::normalizeFieldEntry(
			$this->entry(
				array(
					'id'      => 'size',
					'options' => array( ' Small ', 'Medium', 'Large' ),
					'default' => 'Medium',
				),
				'select'
			),
			true
		);

		$this->assertNotNull( $normalized );
		$this->assertSame( array( 'Small', 'Medium', 'Large' ), $normalized['options'] );
		$this->assertSame( 'Medium', $normalized['default'] );
	}

	public function testStrictModeRejectsCheckboxWithOptions(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry(
				$this->entry( array( 'id' => 'agree', 'options' => array( 'Yes', 'No' ) ), 'checkbox' ),
				true
			)
		);
	}

	public function testStrictCheckboxWithEmptyOptionsAndDefaultRoundTrips(): void {
		$normalized = FieldRepository::normalizeFieldEntry(
			$this->entry(
				array(
					'id'      => 'agree',
					'options' => array(),
					'default' => '',
				),
				'checkbox'
			),
			true
		);

		$this->assertNotNull( $normalized );
		$this->assertSame( array(), $normalized['options'] );
		$this->assertSame( '', $normalized['default'] );
	}

	public function testFieldTransferStyleRoundTripKeepsChoiceConfig(): void {
		$config = array(
			array(
				'id'          => 'size',
				'label'       => 'Size',
				'type'        => 'select',
				'required'    => true,
				'enabled'     => true,
				'placeholder' => 'Pick one',
				'description' => 'Choose a size',
				'default'     => 'M',
				'options'     => array( 'S', 'M', 'L' ),
				'rules'       => array(),
			),
			array(
				'id'          => 'agree',
				'label'       => 'Agree',
				'type'        => 'checkbox',
				'required'    => true,
				'enabled'     => true,
				'placeholder' => '',
				'description' => '',
				'default'     => '1',
				'options'     => array(),
				'rules'       => array(),
			),
		);

		$clean = array();
		foreach ( $config as $entry ) {
			$normalized = FieldRepository::normalizeFieldEntry( $entry, true );

			$this->assertNotNull( $normalized );
			$clean[ $normalized['id'] ] = $normalized;
		}

		$this->assertSame( array( 'S', 'M', 'L' ), $clean['size']['options'] );
		$this->assertSame( 'M', $clean['size']['default'] );
		$this->assertSame( array(), $clean['agree']['options'] );
		$this->assertSame( '1', $clean['agree']['default'] );
	}

	public function testStrictModeRejectsEmptyOptionList(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry( $this->entry( array( 'options' => array() ), 'radio' ), true )
		);
	}

	public function testStrictModeRejectsEmptyOptionString(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry( $this->entry( array( 'options' => array( 'A', '' ) ), 'radio' ), true )
		);
	}

	public function testStrictModeRejectsDuplicateOptions(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry( $this->entry( array( 'options' => array( 'A', 'A' ) ), 'radio' ), true )
		);
	}

	public function testStrictModeRejectsAssociativeOptionPayload(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry( $this->entry( array( 'options' => array( 'a' => 'A', 'b' => 'B' ) ), 'radio' ), true )
		);
	}

	public function testStrictModeRejectsDefaultOutsideOptions(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry(
				$this->entry( array( 'options' => array( 'A', 'B' ), 'default' => 'C' ), 'select' ),
				true
			)
		);
	}

	public function testStrictModeRejectsDefaultOnChoiceWithoutOptions(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry( $this->entry( array( 'default' => 'A' ), 'radio' ), true )
		);
	}

	public function testCheckboxDefaultIsNormalized(): void {
		$normalized = FieldRepository::normalizeFieldEntry(
			$this->entry( array( 'id' => 'agree', 'default' => true ), 'checkbox' ),
			true
		);

		$this->assertNotNull( $normalized );
		$this->assertSame( '1', $normalized['default'] );
	}

	public function testStrictModeRejectsInvalidCheckboxDefault(): void {
		$this->assertNull(
			FieldRepository::normalizeFieldEntry( $this->entry( array( 'id' => 'agree', 'default' => 'yes' ), 'checkbox' ), true )
		);
	}

	public function testValidateFieldsRejectsWholeConfiguration(): void {
		$good = $this->entry( array( 'id' => 'good' ) );
		$bad  = $this->entry( array( 'id' => 'bad', 'type' => 'select', 'options' => array( 'A', 'A' ) ) );

		$this->assertNull( FieldRepository::validateFields( array( $good, $bad ) ) );
	}

	public function testRulesArePreservedVerbatim(): void {
		$rules      = array( array( 'field' => 'other', 'op' => '!=', 'value' => '' ) );
		$normalized = FieldRepository::normalizeFieldEntry(
			$this->entry( array( 'id' => 'gated', 'rules' => $rules ) ),
			true
		);

		$this->assertNotNull( $normalized );
		$this->assertSame( $rules, $normalized['rules'] );
	}

	public function testRulesArePreservedInStrictValidation(): void {
		$rules = array( 'source' => 'pro' );
		$clean = FieldRepository::validateFields( array( $this->entry( array( 'id' => 'gated', 'rules' => $rules ) ) ) );

		$this->assertNotNull( $clean );
		$this->assertSame( $rules, $clean['gated']['rules'] );
	}

	public function testRuntimeSanitizerToleratesBrokenExtras(): void {
		$fields = FieldRepository::sanitizeFields(
			array( $this->entry( array( 'options' => 'not-a-list', 'default' => array() ) ) )
		);

		$this->assertArrayHasKey( 'pickup', $fields );
		$this->assertSame( array(), $fields['pickup']['options'] );
		$this->assertSame( '', $fields['pickup']['default'] );
	}

	public function testGetCarriesChoiceConfigIntoFieldObjects(): void {
		$GLOBALS['scfw_test_options'] = array(
			'scfw_options' => array(
				'enabled' => 1,
				'fields'  => array(
					array(
						'id'       => 'delivery',
						'label'    => 'Delivery Slot',
						'type'     => 'select',
						'required' => true,
						'enabled'  => true,
						'options'  => array( 'Morning', 'Evening' ),
						'default'  => 'Evening',
						'rules'    => array(),
					),
				),
			),
		);

		$fields = FieldRepository::get();

		$this->assertCount( 1, $fields );
		$this->assertSame( array( 'Morning', 'Evening' ), $fields[0]->getOptions() );
		$this->assertSame( 'Evening', $fields[0]->getDefaultValue() );
		$this->assertSame( FieldType::SELECT, $fields[0]->getType() );
	}

	public function testDefaultConfigIncludesFreeExtras(): void {
		$this->assertSame( array( 'delivery_instructions' ), array_keys( FieldRepository::config() ) );

		$delivery = FieldRepository::config()['delivery_instructions'];

		$this->assertSame( '', $delivery['placeholder'] );
		$this->assertSame( '', $delivery['description'] );
		$this->assertSame( '', $delivery['default'] );
		$this->assertSame( array(), $delivery['options'] );
	}
}