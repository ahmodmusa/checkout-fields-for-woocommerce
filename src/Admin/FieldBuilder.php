<?php
/**
 * Checkout field builder.
 *
 * @package SCFW\Admin
 */

declare(strict_types=1);

namespace SCFW\Admin;

use SCFW\Core\Field\FieldRepository;
use SCFW\Core\Field\FieldType;

/**
 * Renders the admin builder for the configured checkout fields.
 *
 * The builder is pure presentation. Field resolution and normalization stay in
 * the Core field repository; this class only turns the current configuration
 * into native WordPress admin form controls. The created field types come
 * directly from the Core FieldType enum.
 *
 * @since 0.0.1
 */
final class FieldBuilder {

	/**
	 * Render the field builder table and add-field controls.
	 *
	 * Every control posts through the existing `scfw_options` settings group so
	 * the WordPress Settings API continues to own nonces, sanitization and
	 * persistence. The row order in the table is the configured checkout order.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function render(): void {
		$config    = FieldRepository::config();
		$typeCases = FieldType::cases();
		?>
		<p class="description">
			<?php esc_html_e( 'Configure the checkout fields. Drag the handle to reorder, or use the row buttons to move, remove or disable a field.', 'checkout-fields-for-woocommerce' ); ?>
		</p>
		<table class="widefat striped scfw-field-builder" id="scfw-field-builder">
			<thead>
				<tr>
					<th class="scfw-col-reorder" aria-hidden="true"></th>
					<th><?php esc_html_e( 'ID', 'checkout-fields-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Label', 'checkout-fields-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Type', 'checkout-fields-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Required', 'checkout-fields-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Enabled', 'checkout-fields-for-woocommerce' ); ?></th>
					<th><?php esc_html_e( 'Actions', 'checkout-fields-for-woocommerce' ); ?></th>
				</tr>
			</thead>
			<tbody id="scfw-fields">
			<?php foreach ( array_values( $config ) as $index => $field ) : ?>
				<tr data-row="<?php echo esc_attr( (string) $index ); ?>">
					<td class="scfw-col-reorder">
						<span class="scfw-handle" draggable="true" title="<?php esc_attr_e( 'Drag to reorder', 'checkout-fields-for-woocommerce' ); ?>">&#8942;</span>
					</td>
					<td>
						<input type="text" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][id]" value="<?php echo esc_attr( (string) $field['id'] ); ?>" />
					</td>
					<td>
						<input type="text" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][label]" value="<?php echo esc_attr( (string) $field['label'] ); ?>" />
					</td>
					<td>
						<select name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][type]">
							<?php foreach ( $typeCases as $type ) : ?>
								<option value="<?php echo esc_attr( $type->value ); ?>" <?php selected( (string) $field['type'], $type->value, true ); ?>>
									<?php echo esc_html( $type->value ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
					<td>
						<input type="hidden" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][required]" value="0" />
						<input type="checkbox" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][required]" value="1" <?php checked( (bool) $field['required'], true ); ?> />
					</td>
					<td>
						<input type="hidden" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][enabled]" value="0" />
						<input type="checkbox" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][enabled]" value="1" <?php checked( (bool) $field['enabled'], true ); ?> />
					</td>
					<td class="scfw-actions">
						<button type="submit" class="button-link" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][move]" value="up">
							<?php esc_html_e( 'Up', 'checkout-fields-for-woocommerce' ); ?>
						</button>
						<span aria-hidden="true">|</span>
						<button type="submit" class="button-link" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][move]" value="down">
							<?php esc_html_e( 'Down', 'checkout-fields-for-woocommerce' ); ?>
						</button>
						<span aria-hidden="true">|</span>
						<button type="submit" class="button-link scfw-remove" name="scfw_options[fields][<?php echo esc_attr( (string) $index ); ?>][remove]" value="1">
							<?php esc_html_e( 'Remove', 'checkout-fields-for-woocommerce' ); ?>
						</button>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<fieldset class="scfw-add-field">
			<legend><?php esc_html_e( 'Add New Field', 'checkout-fields-for-woocommerce' ); ?></legend>
			<p>
				<label for="scfw-new-type">
					<?php esc_html_e( 'Type', 'checkout-fields-for-woocommerce' ); ?>
					<select id="scfw-new-type" name="scfw_options[new_field][type]">
						<?php foreach ( $typeCases as $type ) : ?>
							<option value="<?php echo esc_attr( $type->value ); ?>"><?php echo esc_html( $type->value ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>
				<label for="scfw-new-label">
					<?php esc_html_e( 'Label', 'checkout-fields-for-woocommerce' ); ?>
					<input id="scfw-new-label" type="text" name="scfw_options[new_field][label]" value="" />
				</label>
				<?php submit_button( __( 'Add Field', 'checkout-fields-for-woocommerce' ), 'secondary', 'scfw_options[add]', false ); ?>
			</p>
		</fieldset>
		<?php
	}
}
