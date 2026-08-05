<?php
/**
 * Settings.
 *
 * @package SCFW\Admin
 */

declare(strict_types=1);

namespace SCFW\Admin;

/**
 * Registers the plugin settings.
 *
 * @since 0.0.1
 */
final class Settings {

	/**
	 * Settings option name.
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	private const OPTION_NAME = 'scfw_options';

	/**
	 * Register the WordPress settings API.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		register_setting(
			'scfw',
			self::OPTION_NAME,
			array(
				'type'         => 'array',
				'show_in_rest' => false,
				'default'      => array(),
				'autoload'     => true,
			)
		);

		add_settings_section(
			'scfw_general',
			__( 'General Settings', 'checkout-fields-for-woocommerce' ),
			'',
			'scfw'
		);

		add_settings_field(
			'enabled',
			__( 'Enable Plugin', 'checkout-fields-for-woocommerce' ),
			array( self::class, 'renderEnabledField' ),
			'scfw',
			'scfw_general'
		);
	}

	/**
	 * Render the enabled checkbox field.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function renderEnabledField(): void {
		$options = get_option( self::OPTION_NAME, array() );
		?>
		<label for="enabled">
			<input type="hidden" name="scfw_options[enabled]" value="0" />
			<input type="checkbox" id="enabled" name="scfw_options[enabled]" value="1" <?php checked( ! empty( $options['enabled'] ), true ); ?> />
			<?php echo esc_html__( 'Enable the plugin.', 'checkout-fields-for-woocommerce' ); ?>
		</label>
		<?php
	}
}