<?php
/**
 * Admin menu.
 *
 * @package SCFW\Admin
 */

declare(strict_types=1);

namespace SCFW\Admin;

/**
 * Registers the plugin admin menu under WooCommerce and routes its admin pages.
 *
 * The single menu entry "Checkout Fields" is registered under WooCommerce and
 * defaults to the "Fields" tab. Settings and Import / Export are accessible
 * via the unified header tabs and direct URLs, keeping the WordPress sidebar
 * clean and uncluttered.
 *
 * @since 0.0.1
 */
final class AdminMenu {

	/**
	 * Admin page hooks the plugin assets load on.
	 *
	 * @since 0.0.1
	 *
	 * @var array<int, string>
	 */
	private static array $page_hooks = array();

	/**
	 * Register the plugin admin menu under WooCommerce.
	 *
	 * Only a single "Checkout Fields for WooCommerce" entry is registered under
	 * the WooCommerce menu. Fields, Settings, and Import / Export are navigated
	 * seamlessly as internal tabs within this single admin screen.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function register(): void {
		self::$page_hooks = array();

		// Primary entry under WooCommerce -> Checkout Fields for WooCommerce.
		$hook = add_submenu_page(
			'woocommerce',
			__( 'Checkout Fields for WooCommerce', 'checkout-fields-for-woocommerce' ),
			__( 'Checkout Fields for WooCommerce', 'checkout-fields-for-woocommerce' ),
			'manage_options',
			'scfw-fields',
			array( self::class, 'renderPage' )
		);

		if ( is_string( $hook ) ) {
			self::$page_hooks[] = $hook;
		}
	}

	/**
	 * Move the plugin submenu item to the absolute end of the WooCommerce submenu.
	 *
	 * WooCommerce core and extensions register and sort their submenu items at
	 * varying priorities. Hooking into `admin_menu` at late priority (999) allows
	 * us to safely locate our submenu entry by its slug ('scfw-fields') and move it
	 * to the very end of `$GLOBALS['submenu']['woocommerce']` without index guessing
	 * or duplicating entries.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function reorderSubmenu(): void {
		global $submenu;

		if ( ! is_array( $submenu ) || empty( $submenu['woocommerce'] ) || ! is_array( $submenu['woocommerce'] ) ) {
			return;
		}

		$target_key  = null;
		$target_item = null;

		foreach ( $submenu['woocommerce'] as $key => $item ) {
			if ( is_array( $item ) && isset( $item[2] ) && 'scfw-fields' === $item[2] ) {
				$target_key  = $key;
				$target_item = $item;
				break;
			}
		}

		if ( null !== $target_key && null !== $target_item ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Safely relocating plugin submenu entry to end of WooCommerce submenu array.
			unset( $submenu['woocommerce'][ $target_key ] );
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$submenu['woocommerce'][] = $target_item;
		}
	}

	/**
	 * Main admin page router that renders the appropriate content based on the active tab.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function renderPage(): void {
		// Determine active tab from $_GET['tab'] or fallback to 'fields'.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active_tab = isset( $_GET['tab'] ) && is_string( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'fields';

		if ( 'settings' === $active_tab ) {
			self::renderSettings();
		} elseif ( 'transfer' === $active_tab ) {
			self::renderTransfer();
		} else {
			self::renderFields();
		}
	}

	/**
	 * Render the unified plugin header with title, subtitle, tabs, and optional action.
	 *
	 * @since 0.0.1
	 *
	 * @param string $active_tab   Active tab slug ('fields', 'settings', 'transfer').
	 * @param bool   $show_add_btn Whether to show the top-right 'Add New Field' button.
	 *
	 * @return void
	 */
	public static function renderHeader( string $active_tab = 'fields', bool $show_add_btn = false ): void {
		?>
		<header class="scfw-app-header">
			<div class="scfw-app-header__main">
				<div class="scfw-app-header__title-group">
					<h1 class="scfw-app-title"><?php echo esc_html__( 'Checkout Fields', 'checkout-fields-for-woocommerce' ); ?></h1>
					<p class="scfw-app-subtitle"><?php esc_html_e( 'Create, manage and display custom fields on your WooCommerce checkout.', 'checkout-fields-for-woocommerce' ); ?></p>
				</div>
				<?php if ( $show_add_btn ) : ?>
					<div class="scfw-app-header__actions">
						<button type="button" class="button button-primary scfw-btn-primary scfw-trigger-add-field" id="scfw-top-add-btn">
							<span class="dashicons dashicons-plus" aria-hidden="true"></span>
							<?php esc_html_e( 'Add New Field', 'checkout-fields-for-woocommerce' ); ?>
						</button>
					</div>
				<?php endif; ?>
			</div>

			<nav class="scfw-nav-tabs" aria-label="<?php esc_attr_e( 'Checkout Fields Navigation', 'checkout-fields-for-woocommerce' ); ?>">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=scfw-fields' ) ); ?>" class="scfw-nav-tab <?php echo 'fields' === $active_tab ? 'is-active' : ''; ?>">
					<?php esc_html_e( 'Fields', 'checkout-fields-for-woocommerce' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=scfw-fields&tab=settings' ) ); ?>" class="scfw-nav-tab <?php echo 'settings' === $active_tab ? 'is-active' : ''; ?>">
					<?php esc_html_e( 'Settings', 'checkout-fields-for-woocommerce' ); ?>
				</a>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=scfw-fields&tab=transfer' ) ); ?>" class="scfw-nav-tab <?php echo 'transfer' === $active_tab ? 'is-active' : ''; ?>">
					<?php esc_html_e( 'Import / Export', 'checkout-fields-for-woocommerce' ); ?>
				</a>
			</nav>
		</header>
		<?php
	}

	/**
	 * Render the Fields admin page.
	 *
	 * The FieldBuilder renders inside the WordPress settings form for the
	 * `scfw-fields` settings section so the existing Settings API owns the
	 * nonces, sanitization and persistence.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function renderFields(): void {
		?>
		<div class="wrap scfw-admin scfw-admin--fields">
			<?php self::renderHeader( 'fields', true ); ?>
			<?php settings_errors( 'scfw' ); ?>

			<div class="scfw-fields-form">
				<form method="post" action="options.php" id="scfw-fields-form">
					<?php
					settings_fields( 'scfw' );
					do_settings_sections( 'scfw-fields' );
					?>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the Import / Export admin page.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function renderTransfer(): void {
		?>
		<div class="wrap scfw-admin scfw-admin--transfer">
			<?php self::renderHeader( 'transfer', false ); ?>
			<?php settings_errors( 'scfw' ); ?>
			<?php FieldTransfer::render(); ?>
		</div>
		<?php
	}

	/**
	 * Render the Settings admin page.
	 *
	 * @since 0.0.1
	 *
	 * @return void
	 */
	public static function renderSettings(): void {
		?>
		<div class="wrap scfw-admin scfw-admin--settings">
			<?php self::renderHeader( 'settings', false ); ?>
			<?php settings_errors( 'scfw' ); ?>

			<div class="scfw-settings-panel">
				<form method="post" action="options.php">
					<?php
					settings_fields( 'scfw' );
					do_settings_sections( 'scfw-settings' );
					submit_button( __( 'Save Settings', 'checkout-fields-for-woocommerce' ), 'primary scfw-btn-primary' );
					?>
				</form>
			</div>

			<section class="scfw-card scfw-compatibility-card" aria-labelledby="scfw-compatibility-title">
				<h2 class="scfw-card-title" id="scfw-compatibility-title">
					<?php esc_html_e( 'Compatibility', 'checkout-fields-for-woocommerce' ); ?>
				</h2>
				<p class="scfw-card-desc">
					<?php esc_html_e( 'Checkout Fields for WooCommerce is built to work with modern WooCommerce checkout and order systems.', 'checkout-fields-for-woocommerce' ); ?>
				</p>

				<div class="scfw-compatibility-grid">
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'Classic Checkout', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'WooCommerce Checkout Block', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'HPOS (High-Performance Order Storage)', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'Guest Checkout', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'Logged-in Customer Checkout', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'WooCommerce Order Admin', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'Thank You Page', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'My Account Orders', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'WooCommerce Emails', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
					<div class="scfw-compatibility-item">
						<span class="scfw-compat-icon" aria-hidden="true">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</span>
						<span class="scfw-compat-label"><?php esc_html_e( 'WooCommerce REST API', 'checkout-fields-for-woocommerce' ); ?></span>
					</div>
				</div>

				<p class="scfw-compatibility-note">
					<?php esc_html_e( 'Custom checkout field data is stored using WooCommerce-compatible order metadata and is designed to support both legacy order storage and HPOS.', 'checkout-fields-for-woocommerce' ); ?>
				</p>
			</section>
		</div>
		<?php
	}

	/**
	 * Enqueue the admin assets on the plugin admin pages.
	 *
	 * Assets load only on the plugin pages, never on the frontend or on
	 * other admin screens.
	 *
	 * @since 0.0.1
	 *
	 * @param string $hook_suffix Current admin page hook.
	 *
	 * @return void
	 */
	public static function enqueueAssets( string $hook_suffix ): void {
		if ( ! in_array( $hook_suffix, self::$page_hooks, true ) ) {
			return;
		}

		$style_version  = (string) filemtime( SCFW_PATH . 'assets/admin/admin.css' );
		$script_version = (string) filemtime( SCFW_PATH . 'assets/admin/field-builder.js' );

		wp_enqueue_style( 'scfw-admin', SCFW_URL . 'assets/admin/admin.css', array(), $style_version );
		wp_enqueue_script( 'scfw-field-builder', SCFW_URL . 'assets/admin/field-builder.js', array(), $script_version, true );
	}
}
