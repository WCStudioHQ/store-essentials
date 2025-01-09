<?php

namespace WCSTUDIO_STORE_ESSENTIALS;

/**
 * Class WCSES_Admin
 *
 * Handles the admin functionality of the WooCommerce Sorting Toggle plugin.
 * This includes adding menu items to the WordPress admin dashboard, registering settings,
 * and rendering the plugin's settings page.
 */
class WCSES_Admin {

	/**
	 * Constructor method.
	 *
	 * Initializes the Admin class by hooking into WordPress actions for setting up
	 * the admin menu and registering plugin settings. This ensures the class functions
	 * are executed at the appropriate points in the WordPress lifecycle.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wcses_add_plugin_admin_menu' ) );
		add_action( 'admin_post_wcses_sort_toggle_settings', array( $this, 'wcses_save_sort_toggle_settings' ) );
		add_action( 'admin_post_wcses_min_max_quantity_options', array( $this, 'wcses_save_min_max_settings' ) );
	}

	/**
	 * Registers the plugin's admin menu and Submenu in the WordPress dashboard.
	 *
	 * Adds a custom menu item in the WordPress admin sidebar for accessing
	 * the plugin's settings and options. This method is typically hooked to
	 * WordPress's `admin_menu` action.
	 *
	 * @return void
	 */
	public function wcses_add_plugin_admin_menu() {
			add_menu_page(
				__( 'Store Essentials', 'store-essentials' ),
				__( 'Store Essentials', 'store-essentials' ),
				'manage_options',
				'store-essentials',
				array( $this, 'wcses_render_settings_page' ),
				'dashicons-filter',
				20
			);
			add_submenu_page(
				'store-essentials',
				__( 'Sorting Toggle', 'store-essentials' ),
				__( 'Sorting Toggle', 'store-essentials' ),
				'manage_options',
				'wcses-sort-toggling',
				array( $this, 'wcses_render_settings_page' )
			);
			add_submenu_page(
				'store-essentials',
				__( 'Min Max Quantity Settings', 'store-essentials' ),
				__( 'Min Max Quantity', 'store-essentials' ),
				'manage_options',
				'wcses-min-max-quantity',
				array( $this, 'wcses_settings_page_html' )
			);
			remove_submenu_page( 'store-essentials', 'store-essentials' );
	}

	/**
	 * Renders the settings page for the plugin.
	 *
	 * Displays a form in the WordPress admin where users can choose to hide
	 * WooCommerce sorting options based on their selected position: 'none', 'before',
	 * 'after', or 'both'.
	 *
	 * @return void
	 */
	public function wcses_render_settings_page() {
		$sorting_position = get_option( 'wcses_sort_toggle', 'none' );
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Shop Sorting Toggle', 'store-essentials' ); ?></h1>
			<form method="post"  action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Hide Sorting Options', 'store-essentials' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input type="radio" name="shop_sort_toggling_position" value="none" <?php checked( 'none', $sorting_position ); ?>>
									<?php esc_html_e( 'Do not hide', 'store-essentials' ); ?>
								</label><br>
								<label>
									<input type="radio" name="shop_sort_toggling_position" value="before" <?php checked( 'before', $sorting_position ); ?>>
									<?php esc_html_e( 'Hide Before Shop Loop', 'store-essentials' ); ?>
								</label><br>
								<label>
									<input type="radio" name="shop_sort_toggling_position" value="after" <?php checked( 'after', $sorting_position ); ?>>
									<?php esc_html_e( 'Hide After Shop Loop', 'store-essentials' ); ?>
								</label><br>
								<label>
									<input type="radio" name="shop_sort_toggling_position" value="both" <?php checked( 'both', $sorting_position ); ?>>
									<?php esc_html_e( 'Hide Both', 'store-essentials' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
				</table>
				<?php wp_nonce_field( 'wcses_sort_toggle_nonce' ); ?>
				<input type="hidden" name="action" value="wcses_sort_toggle_settings">

				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handles the saving of the Shop Sorting Toggle settings.
	 *
	 * This function processes the form submission for the Shop Sorting Toggle settings page.
	 * It verifies the nonce, checks the user permissions, sanitizes the input, saves the settings
	 * to the WordPress options table, and redirects back to the settings page with a success status.
	 */
	public function wcses_save_sort_toggle_settings() {
		check_admin_referer( 'wcses_sort_toggle_nonce' );
		if ( ! isset( $_POST['action'] ) || 'wcses_sort_toggle_settings' !== $_POST['action'] ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$position = isset( $_POST['shop_sort_toggling_position'] ) ? sanitize_text_field( wp_unslash( $_POST['shop_sort_toggling_position'] ) ) : 'none';
		update_option( 'wcses_sort_toggle', $position );

		wp_safe_redirect( admin_url( 'admin.php?page=wcses-sort-toggling' ) );
		exit;
	}

	/**
	 * Renders the settings page for the plugin.
	 *
	 * Displays a form in the WordPress admin where users can define the Min and Max Quantity settings.
	 *
	 * @return void
	 */
	public function wcses_settings_page_html() {
		$options      = get_option( 'wcses_min_max_quantity_options', array() );
		$min_quantity = isset( $options['min'] ) ? $options['min'] : '';
		$max_quantity = isset( $options['max'] ) ? $options['max'] : '';
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Min Max Quantity Settings', 'store-essentials' ); ?></h1>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" >
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Minimum Quantity', 'store-essentials' ); ?></th>
						<td>
							<input type="number" name="min_max_quantity_options[min]" value="<?php echo esc_attr( $min_quantity ); ?>" placeholder="<?php esc_attr_e( 'Enter minimum quantity', 'store-essentials' ); ?>" />
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php esc_html_e( 'Maximum Quantity', 'store-essentials' ); ?></th>
						<td>
							<input type="number" name="min_max_quantity_options[max]" value="<?php echo esc_attr( $max_quantity ); ?>" placeholder="<?php esc_attr_e( 'Enter maximum quantity', 'store-essentials' ); ?>" />
						</td>
					</tr>
				</table>
				<?php wp_nonce_field( 'wcses_mmq_nonce' ); ?>
				<input type="hidden" name="action" value="wcses_min_max_quantity_options">
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Handles saving the Min/Max Quantity settings.
	 *
	 * This function processes the submitted form data from the "Min Max Quantity Settings" page.
	 * It verifies the submitted data for security and sanitizes the input before saving it
	 * to the WordPress options table. After processing, it redirects the user to the settings page
	 * with a success or error status.
	 *
	 * @return void
	 */
	public function wcses_save_min_max_settings() {
		check_admin_referer( 'wcses_mmq_nonce' );
		if ( ! isset( $_POST['action'] ) || 'wcses_min_max_quantity_options' !== $_POST['action'] ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$min = isset( $_POST['min_max_quantity_options']['min'] ) ? absint( wp_unslash( $_POST['min_max_quantity_options']['min'] ) ) : 0;
		$max = isset( $_POST['min_max_quantity_options']['max'] ) ? absint( wp_unslash( $_POST['min_max_quantity_options']['max'] ) ) : 0;

		if ( $max > 0 && $min > $max ) {
			wp_safe_redirect( admin_url( 'admin.php?page=wcses-min-max-quantity&status=error' ) );
			exit;
		}
		$options = array(
			'min' => $min,
			'max' => $max,
		);
		update_option( 'wcses_min_max_quantity_options', $options );
		wp_safe_redirect( admin_url( 'admin.php?page=wcses-min-max-quantity' ) );
		exit;
	}
}