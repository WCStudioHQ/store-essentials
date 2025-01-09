<?php

namespace WCSTUDIO_STORE_ESSENTIALS;

/**
 * Class WCSES_Frontend
 *
 * Handles the frontend functionality of the WooCommerce Sorting Toggle plugin.
 * This includes controlling the visibility of sorting options on WooCommerce shop pages
 * and enqueuing necessary scripts for the toggle functionality.
 */
class WCSES_Frontend {

	/**
	 * Constructor method.
	 *
	 * Initializes the Frontend class by setting up actions for:
	 * - Controlling the visibility of the sorting dropdown on WooCommerce shop pages.
	 * - Enqueuing and localizing JavaScript files used for the toggle functionality.
	 */
	public function __construct() {
		add_action( 'woocommerce_before_shop_loop', array( $this, 'wcses_control_sorting_visibility' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wcses_scripts' ) );
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'wcses_validate_single_page_min_max_cart_quantity' ), 10, 3 );
		add_action( 'woocommerce_after_cart_item_quantity_update', array( $this, 'wcses_validate_and_correct_cart_quantities' ), 10, 3 );
	}
	/**
	 * Enqueues and localizes the script for the WooCommerce sorting dropdown toggle.
	 *
	 * This method ensures that the JavaScript file is loaded on the frontend,
	 * and it passes necessary data, such as the sorting toggle position and current theme,
	 * to the script for dynamic behavior.
	 *
	 * @return void
	 */
	public function wcses_scripts() {
		wp_enqueue_script( 'wcses-sort-toggle', WCSTUDIO_STORE_ESSENTIALS_URL . 'assets/js/theme-check.js', array( 'jquery' ), WCSTUDIO_STORE_ESSENTIALS_VERSION, true );
		wp_localize_script(
			'wcses-sort-toggle',
			'wcsesSortToggle',
			array(
				'position'      => get_option( 'wcses_sort_toggle', 'none' ),
				'current_theme' => get_option( 'template' ),
			)
		);
	}

	/**
	 * Controls the visibility of sorting options on WooCommerce pages.
	 *
	 * This method dynamically removes the default WooCommerce sorting dropdown
	 * from specific positions on the shop pages based on the user's settings.
	 * The visibility can be toggled for 'before' the shop loop, 'after' the shop loop,
	 * or both positions.
	 *
	 * @return void
	 */
	public function wcses_control_sorting_visibility() {
		$sorting_position = get_option( 'wcses_sort_toggle', 'none' );
		if ( is_woocommerce() || is_cart() || is_checkout() ) {
			if ( is_shop() || is_product_category() || is_product_tag() ) {
				$priority = has_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering' );
				if ( false !== $priority && ( 'before' === $sorting_position || 'both' === $sorting_position ) ) {
					remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', $priority );
				}

				if ( false !== $priority && ( 'after' === $sorting_position || 'both' === $sorting_position ) ) {
					remove_action( 'woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10 );
				}
			}
		}
	}

	/**
	 * Check min and max quantity rules for single product page.
	 *
	 * @param bool $passed Indicates whether adding to cart is allowed.
	 * @param int  $product_id ID of the product to check.
	 * @param int  $quantity Quantity to validate.
	 * @return bool
	 */
	public function wcses_validate_single_page_min_max_cart_quantity( $passed, $product_id, $quantity ) {
		$options = get_option( 'wcses_min_max_quantity_options' );

		$min_quantity             = isset( $options['min'] ) ? $options['min'] : 1;
		$max_quantity             = isset( $options['max'] ) ? $options['max'] : 1;
		$cart_items               = WC()->cart->get_cart();
		$current_product_quantity = 0;

		foreach ( $cart_items as $cart_item ) {
			if ( $cart_item['product_id'] === $product_id ) {
				$current_product_quantity += $cart_item['quantity'];
			}
		}
		$total_quantity = $current_product_quantity + $quantity;

		if ( $total_quantity < $min_quantity ) {
			// Translators: %d is the minimum quantity required for this product.
			wc_add_notice( sprintf( __( 'You need to add at least %d of this product.', 'store-essentials' ), $min_quantity ), 'error' );
			return false;
		}

		if ( $total_quantity > $max_quantity ) {
			// Translators: %d is the maximum quantity allowed for the product.
			wc_add_notice( sprintf( __( 'You cannot add more than %d of this product.', 'store-essentials' ), $max_quantity ), 'error' );
			return false;
		}

		return $passed;
	}

	/**
	 * Validate and correct cart quantities based on minimum and maximum limits.
	 *
	 * This function iterates through all items in the cart, checks their quantities
	 * against the minimum and maximum limits specified in the plugin's settings,
	 * and adjusts the quantities if they are outside the allowed range. Notices
	 * are added to inform the user if any corrections are made.
	 */
	public function wcses_validate_and_correct_cart_quantities() {
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$current_quantity = $cart_item['quantity'];

			$options = get_option( 'wcses_min_max_quantity_options' );

			$min_qty = isset( $options['min'] ) ? $options['min'] : 1;
			$max_qty = isset( $options['max'] ) ? $options['max'] : 12;

			if ( $current_quantity > $max_qty ) {
				WC()->cart->set_quantity( $cart_item_key, $max_qty, true );
				wc_add_notice(
					sprintf( 'The maximum quantity allowed for this product is %d. Quantity has been reset.', $max_qty ),
					'error'
				);
				return;
			}
			if ( $current_quantity < $min_qty ) {
				WC()->cart->set_quantity( $cart_item_key, $min_qty, true );
				wc_add_notice(
					sprintf( 'The minimum quantity allowed for this product is %d. Quantity has been reset.', $min_qty ),
					'error'
				);
				return;
			}
		}
	}
}
