<?php
/**
 * Register custom post types
 *
 * @package     MySiteName
 */
class MySiteName_WooCommerce {

	/**
   * Initialize the class
   */
	public function __construct() {
		add_action('after_setup_theme', 'MySiteName_add_woocommerce_support');
		add_action('after_setup_theme', 'MySiteName_woocommerce_setup');
		add_action('after_setup_theme', 'MySiteName_remove_add_move_woocommerce_stuff');
		add_filter('body_class', 'MySiteName_shop_body_class');
		add_filter('woocommerce_product_tabs', 'MySiteName_rename_tabs', 98);
		add_filter('wp_nav_menu_items','MySiteName_menucart', 10, 2);
		add_action('woocommerce_before_main_content', 'MySiteName_add_shop_loop_wrapper_open', 40);
		add_action('woocommerce_after_main_content', 'MySiteName_add_shop_loop_wrapper_close', 5);
		add_action('woocommerce_before_shop_loop_item_title', 'MySiteName_add_product_title_and_price_wrapper_open', 20);
		add_action('woocommerce_after_shop_loop_item_title', 'MySiteName_add_product_title_and_price_wrapper_close', 20);
		add_filter('woocommerce_ship_to_different_address_checked', '__return_true'); // Ship to a different address opened by default
		remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10); // Move Login @ WooCommerce Checkout
		add_action('MySiteName_checkout_login_form_hook', 'woocommerce_checkout_login_form');
		add_filter('woocommerce_default_address_fields', 'MySiteName_reorder_checkout_fields');
		add_filter('woocommerce_checkout_fields', 'MySiteName_set_billing_fields'); // Change types and placeholder text for checkout billing fields
		add_filter('woocommerce_checkout_fields', 'MySiteName_set_shipping_fields'); // Change types and placeholder text for checkout shipping fields
	}

	/**
	 * Add WooCommerce support.
	 */
	function MySiteName_add_woocommerce_support()
	{
		add_theme_support('woocommerce', array(
			'thumbnail_image_width' => 400,
			'single_image_width'    => 1200,

			'product_grid'          => array(
				'default_rows'    => 4,
				'min_rows'        => 2,
				'max_rows'        => 8,
				'default_columns' => 1,
				'min_columns'     => 4,
				'max_columns'     => 4,
			),
		));
	}

	/**
	 * Add other WooCommerce features support. ie. Gallery, Lightbox, Slider
	 */
	function MySiteName_woocommerce_setup()
	{
		add_theme_support('wc-product-gallery-zoom');
		add_theme_support('wc-product-gallery-lightbox');
		add_theme_support('wc-product-gallery-slider');
	}

	/**
	 * Remove/Add/Move Stuff
	 */
	function MySiteName_remove_add_move_woocommerce_stuff() {
		add_filter('woocommerce_enqueue_styles', '__return_empty_array'); // Remove default WooCommerce styles
		
		remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10); // Remove Single Product Description tab
		add_action('woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 35); // Add Single Product Description tab beneath Summary

		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40); 	// Remove product category meta

		// remove_action( '', 'woocommerce_quantity_input', 10 ); // Remove quantity input

		remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
		remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
		remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
	}

	/**
	 * Rename product data tabs
	 */
	function MySiteName_rename_tabs($tabs) {
		// $tabs['description']['title'] = __( 'More Information' );		// Rename the description tab
		// $tabs['reviews']['title'] = __( 'Ratings' );						// Rename the reviews tab
		// $tabs['additional_information']['title'] = __( 'Details & Fit' );	// Rename the additional information tab
		unset($tabs['additional_information']); // Remove the additional information tab

		return $tabs;
	}

	/**
	 * Place a cart icon with number of items and total cost in the menu bar.
	 *
	 * Source: http://wordpress.org/plugins/woocommerce-menu-bar-cart/
	 */
	function MySiteName_menucart($menu, $args) {

		// Check if WooCommerce is active and add a new item to a menu assigned to the Shop Menu location
		if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'shop-menu' !== $args->theme_location )
			return $menu;

			ob_start();
			global $woocommerce;
			$viewing_cart = __('View your shopping cart', 'WPSS');
			$start_shopping = __('Start shopping', 'WPSS');
			$cart_url = $woocommerce->cart->get_cart_url();
			$shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
			$cart_contents_count = $woocommerce->cart->cart_contents_count;
			//$cart_contents = sprintf(_n('%d item', '%d items', $cart_contents_count, 'WPSS'), $cart_contents_count);
			$cart_total = $woocommerce->cart->get_cart_total();
			// Uncomment the line below to hide nav menu cart item when there are no items in the cart
			// if ( $cart_contents_count > 0 ) {
				if ($cart_contents_count == 0) {
					$menu_item = '<li class="cart-menu-link"><a class="wcmenucart-contents" href="'. $shop_page_url .'" title="'. $start_shopping .'">';
				} else {
					$menu_item = '<li class="cart-menu-link"><a class="wcmenucart-contents" href="'. $cart_url .'" title="'. $viewing_cart .'">';
				}

				$menu_item .= '<i class="fa fa-shopping-cart"></i> ';

				if ($cart_contents_count == 0) {
				} else {
					$menu_item .= '<div class="cart-count-total">'.$cart_contents_count.'</div>';
					//$menu_item .= $cart_contents.' - '. $cart_total;
				}
				$menu_item .= '</a></li>';
			// Uncomment the line below to hide nav menu cart item when there are no items in the cart
			// }
			echo $menu_item;
		$social = ob_get_clean();
		return $menu . $social;

	}

	/**
	 * Add "shop" class to WooCommerce Shop page
	 */
	function MySiteName_shop_body_class($classes) {
		if (is_shop()) {
			$classes[] = 'shop';
		}

		return $classes;
	}

	/**
	 * Add a .content-width div before shop loop
	 */
	function MySiteName_add_shop_loop_wrapper_open() {
		echo '<div class="content-width">';
	}
	/**
	 * Add a closing </div> to .content-width above
	 */
	function MySiteName_add_shop_loop_wrapper_close() {
		echo '</div><!-- .content-width -->';
	}

	/**
	 * Customize Single Product page sections
	 */
	function MySiteName_add_product_title_and_price_wrapper_open() {
		echo '<div class="product-title-and-price">';
	}
	function MySiteName_add_product_title_and_price_wrapper_close() {
		echo '</div>';
	}

	/**
	 * Create custom hook for login form
	 */
	function MySiteName_checkout_login_form_hook() {
		do_action('MySiteName_checkout_login_form_hook');
	}

	/**
	 * @snippet       Move / ReOrder Address Fields @ Checkout Page, WooCommerce version 3.0+
	 * @how-to        Watch tutorial @ https://businessbloomer.com/?p=19055
	 * @sourcecode    https://businessbloomer.com/?p=19571
	 * @author        Rodolfo Melogli
	 * @testedwith    WooCommerce 3.3.4
	 */
	function MySiteName_reorder_checkout_fields($fields) {

		// default priorities: 
		// 'first_name' - 10
		// 'last_name' - 20
		// 'company' - 30
		// 'country' - 40
		// 'address_1' - 50
		// 'address_2' - 60
		// 'city' - 70
		// 'state' - 80
		// 'postcode' - 90

		// e.g. move 'country' above 'first_name':
		// just assign priority less than 10
		$fields['country']['priority'] = 95;

		return $fields;
	}

	// Our hooked in function - $billing_fields is passed via the filter!
	function MySiteName_set_billing_fields($billing_fields)
	{
		unset($billing_fields['billing']['billing_company']);
		unset($billing_fields['billing']['billing_phone']);

		$billing_fields['billing'] = array(
			'billing_first_name' => array(
				'placeholder' => 'First name',
				'required'    => true
			),
			'billing_last_name' => array(
				'placeholder' => 'Last name',
				'required'    => true
			),
			'billing_address_1' => array(
				'placeholder' => 'Address',
				'required'    => true
			),
			'billing_address_2' => array(
				'placeholder' => 'Apartment, suite, unit, etc. (optional)',
				'required'	  => false
			),
			'billing_city' => array(
				'placeholder' => 'City',
				'required'    => true
			),
			'billing_state' => array(
				'placeholder' => 'State',
				'required'    => true
			),
			'billing_postcode' => array(
				'placeholder' => 'ZIP code',
				'type' 	   => 'tel',
				'required'    => true
			),
			'billing_email' => array(
				'placeholder' => 'Email',
				'type' 	      => 'email',
				'required'    => true
			)
		);
		return $billing_fields;
	}

	// Our hooked in function - $shipping_fields is passed via the filter!
	function MySiteName_set_shipping_fields($shipping_fields)
	{
		unset($shipping_fields['shipping']['shipping_company']);

		$shipping_fields['shipping'] = array(
			'shipping_first_name' => array(
				'placeholder' => 'First name'
			),
			'shipping_last_name' => array(
				'placeholder' => 'Last name'
			),
			'shipping_address_1' => array(
				'placeholder' => 'Address'
			),
			'shipping_address_2' => array(
				'placeholder' => 'Apartment, suite, unit, etc. (optional)'
			),
			'shipping_city' => array(
				'placeholder' => 'City'
			),
			'shipping_state' => array(
				'placeholder' => 'State'
			),
			'shipping_postcode' => array(
				'placeholder' => 'ZIP code',
				'type' 		  => 'tel',
			),
		);
		return $shipping_fields;
	}

	/**
	 * Hide shipping rates when free shipping is available.
	 * Updated to support WooCommerce 2.6 Shipping Zones.
	 *
	 * @param array $rates Array of rates found for the package.
	 * @return array
	 */
	/*
	function MySiteName_hide_shipping_when_free_is_available( $rates ) {
		$free = array();
		foreach ( $rates as $rate_id => $rate ) {
			if ( 'free_shipping' === $rate->method_id ) {
				$free[ $rate_id ] = $rate;
				break;
			}
		}
		return ! empty( $free ) ? $free : $rates;
	}
	add_filter( 'woocommerce_package_rates', 'MySiteName_hide_shipping_when_free_is_available', 100 );

	add_filter( 'http_request_host_is_external', '__return_true' );
	*/

}