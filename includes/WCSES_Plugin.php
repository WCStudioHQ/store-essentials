<?php
namespace WCSTUDIO_STORE_ESSENTIALS;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Plugin Class
 *
 * This class initializes the plugin, sets up core properties and methods,
 * and handles instantiation through a singleton pattern.
 *
 * @package Plugin
 */
class WCSES_Plugin {

	/**
	 * Singleton instance of the Plugin class.
	 *
	 * @var WCSES_Plugin|null
	 */
	public static $instance = null;

	/**
	 * Current version of the plugin.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * The main plugin file.
	 *
	 * @var string
	 */
	public $file;

	/**
	 * Private constructor to prevent multiple instances of the plugin class.
	 *
	 * @param string $file Main plugin file.
	 * @param string $version Plugin version.
	 */
	private function __construct( $file, $version ) {
		$this->version = $version;
		$this->file    = $file;
		$this->define_constants();
		$this->includes();
		$this->activation();
	}

	/**
	 * Retrieves the singleton instance of the Plugin class.
	 *
	 * @param string $file Main plugin file.
	 * @param string $version Plugin version.
	 * @return WCSES_Plugin The singleton instance of the Plugin class.
	 */
	public static function get_instance( $file, $version ) {
		if ( null === self::$instance ) {
			self::$instance = new WCSES_Plugin( $file, $version );
		}
		return self::$instance;
	}

	/**
	 * Defines necessary constants for the plugin.
	 *
	 * @return void
	 */
	private function define_constants() {
		define( 'WCSTUDIO_STORE_ESSENTIALS_VERSION', $this->version );
		define( 'WCSTUDIO_STORE_ESSENTIALS_PATH', plugin_dir_path( $this->file ) );
		define( 'WCSTUDIO_STORE_ESSENTIALS_URL', plugin_dir_url( $this->file ) );
		define( 'WCSTUDIO_STORE_ESSENTIALS_BASENAME', plugin_basename( $this->file ) );
	}

	/**
	 * Includes necessary files for the plugin's functionality.
	 *
	 * @return void
	 */
	private function includes() {
		if ( is_admin() ) {
			new WCSES_Admin();
		}
		new WCSES_Frontend();
	}
	/**
	 * Activation.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function activation() {
		register_activation_hook( $this->file, array( $this, 'wcses_activation_hook' ) );
	}
	/**
	 * Activation hook.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function wcses_activation_hook() {
		$functions = WCSES_Functions::get_instance();
		if ( ! $functions->wcses_wc_ready() ) {
			wp_die(
				'This plugin requires WooCommerce to be active. Please activate WooCommerce and try again.',
				'Plugin Activation Error',
				array( 'back_link' => true )
			);
		}
	}
}
