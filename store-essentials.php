<?php
/**
 * Plugin Name:       Store Essentials
 * Plugin URI:        https://github.com/wcstudiohq/store-essentials
 * Description:       A lightweight plugin to toggle the visibility of WooCommerce sorting options on shop and archive pages.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      7.2
 * Author:            WC Studio
 * Author URI:        https://wcstudio.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       store-essentials
 * Domain Path:       /languages
 * Requires Plugins:  woocommerce
 *
 * @package eShop-sort-toggling
 */

/**
 * store-essentials is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Store Essentials is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Store Essentials. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/vendor/autoload.php';
use WCSTUDIO_STORE_ESSENTIALS\WCSES_Plugin;

/**
 * Initializes the Store Essentials plugin.
 *
 * This function creates and returns a singleton instance of the Plugin class,
 * using the plugin's main file and version as parameters.
 *
 * @return WCSES_Plugin Plugin object instance.
 * @since 1.0.0
 */
function wcstudio_store_essentials_init() {
	return WCSES_Plugin::get_instance( __FILE__, '1.0.0' );
}

wcstudio_store_essentials_init();
