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
		add_action('after_setup_theme', 'MySiteName_woocommerce_setup');
		add_action('after_setup_theme', 'MySiteName_remove_add_move_woocommerce_stuff');
		add_filter('body_class', 'MySiteName_shop_body_class');
		add_filter('woocommerce_product_tabs', 'MySiteName_rename_tabs', 98);
		add_action('woocommerce_before_main_content', 'MySiteName_add_shop_loop_wrapper_open', 40);
		add_action('woocommerce_after_main_content', 'MySiteName_add_shop_loop_wrapper_close', 5);
		add_action('woocommerce_before_shop_loop_item_title', 'MySiteName_add_product_title_and_price_wrapper_open', 20);
		add_action('woocommerce_after_shop_loop_item_title', 'MySiteName_add_product_title_and_price_wrapper_close', 20);
		add_filter('woocommerce_ship_to_different_address_checked', '__return_true'); // Ship to a different address opened by default
		remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_login_form', 10); // Move Login @ WooCommerce Checkout
		add_action('MySiteName_checkout_login_form_hook', 'woocommerce_checkout_login_form');
		add_filter('woocommerce_checkout_fields', 'MySiteName_reorder_checkout_fields');
		add_action('woocommerce_checkout_fields', array( $this, 'MySiteName_set_billing_fields' ) ); // Change types and placeholder text for checkout billing fields
		add_action('woocommerce_checkout_fields', array( $this, 'MySiteName_set_shipping_fields' ) ); // Change types and placeholder text for checkout shipping fields
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
		$fields['shipping']['country']['priority'] = 95;

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
				'type'				=> 'state',
				'placeholder' => 'State',
				'class'				=> array( 'form-row-first'),
				'required'    => true
			),
			'billing_postcode' => array(
				'placeholder' => 'ZIP code',
				'type' 	   => 'tel',
				'class'				=> array( 'form-row-last'),
				'required'    => true
			),
			'billing_country' => array(
				'type' 	      => 'country',
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
				'placeholder' => 'Shipping address'
			),
			'shipping_address_2' => array(
				'placeholder' => 'Apartment, suite, unit, etc. (optional)'
			),
			'shipping_city' => array(
				'placeholder' => 'City'
			),
			'shipping_state' => array(
				'type'				=> 'state',
				'placeholder' => 'State',
				'class'				=> array( 'form-row-first')
			),
			'shipping_postcode' => array(
				'placeholder' => 'ZIP code',
				'type' 		  => 'tel',
				'class'				=> array( 'form-row-last'),
			),
			'shipping_country' => array(
				'type'				=> 'country',
				'placeholder' => 'Country'
			)
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