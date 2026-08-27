<?php
/**
 * Classic saver storage tests.
 *
 * @package SCFW\Tests
 */

declare(strict_types=1);

namespace {

	/**
	 * Minimal WC_Order stand-in for Classic saver unit tests.
	 *
	 * The order-meta surface is all ClassicSaver touches when it persists a
	 * validated value, so the stub only models meta records.
	 */
	class WC_Order {

		/**
		 * Stored meta records.
		 *
		 * @var array<string, mixed>
		 */
		public array $meta = array();

		/**
		 * Store a meta record.
		 *
		 * @param string $key   Meta key.
		 * @param mixed  $value Meta value.
		 * @return void
		 */
		public function update_meta_data( string $key, mixed $value ): void {
			$this->meta[ $key ] = $value;
		}

		/**
		 * Read a meta record.
		 *
		 * @param string $key Meta key.
		 * @return mixed Stored value, or null.
		 */
		public function get_meta( string $key ): mixed {
			return $this->meta[ $key ] ?? null;
		}

		/**
		 * Whether a meta record exists.
		 *
		 * @param string $key Meta key.
		 * @return bool True when the record was written.
		 */
		public function meta_exists( string $key ): bool {
			return array_key_exists( $key, $this->meta );
		}
	}
}

namespace SCFW\Tests {

	use PHPUnit\Framework\TestCase;
	use SCFW\Core\Field\FieldFactory;
	use SCFW\Core\Field\FieldType;
	use SCFW\Frontend\ClassicSaver;

	/**
	 * Verifies the meta records written for Classic checkout fields.
	 */
	final class ClassicSaverTest extends TestCase {

		/**
		 * A freshly saved order under test.
		 *
		 * @return \WC_Order Order stub.
		 */
		private function order(): \WC_Order {
			return new \WC_Order();
		}

		public function testUncheckedOptionalCheckboxWritesNoMeta(): void {
			$order = $this->order();
			$saver = new ClassicSaver();
			$field = FieldFactory::create( 'agree', 'Agree', FieldType::CHECKBOX, false );

			$saver->save( $order, $field, '' );

			$this->assertFalse( $order->meta_exists( '_scfw_agree' ) );
			$this->assertNull( $order->get_meta( '_scfw_agree' ) );
		}

		public function testCheckedCheckboxStoresOne(): void {
			$order = $this->order();
			$saver = new ClassicSaver();
			$field = FieldFactory::create( 'agree', 'Agree', FieldType::CHECKBOX, false );

$saver->save( $order, $field, '1' );

		$this->assertSame( '1', $order->get_meta( '_scfw_agree' ) );
	}

		public function testZeroCheckboxValueStaysStored(): void {
			$order = $this->order();
			$saver = new ClassicSaver();
			$field = FieldFactory::create( 'agree', 'Agree', FieldType::CHECKBOX, false );

			$saver->save( $order, $field, '0' );

			$this->assertTrue( $order->meta_exists( '_scfw_agree' ) );
			$this->assertSame( '0', $order->get_meta( '_scfw_agree' ) );
		}

		public function testEmptyTextValueStillWritesMeta(): void {
			$order = $this->order();
			$saver = new ClassicSaver();
			$field = FieldFactory::create( 'notes', 'Notes', FieldType::TEXT, false );

			$saver->save( $order, $field, '' );

			$this->assertTrue( $order->meta_exists( '_scfw_notes' ) );
			$this->assertSame( '', $order->get_meta( '_scfw_notes' ) );
		}
	}
}