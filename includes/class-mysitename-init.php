<?php
/**
 * Main Init Class
 *
 * @package     MySiteName
 * @subpackage  MySiteName-functionality/includes
 * @copyright   Copyright (c) 2014, Jason Witt
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 * @author      Jason Witt <contact@jawittdesigns.com>
 */
class MySiteName_Init {
	/**
	 * Initialize the class
	 */
	public function __construct() {
		$add_admin_stuff					= new MySiteName_Admin();
		$woocommerce_functions		= new MySiteName_WooCommerce();
		$register_post_types     = new MySiteName_Register_Post_Types();
		$register_taxonomies     = new MySiteName_Register_Taxonomies();
		$remove_admin_bar 	     = new MySiteName_Remove_Admin_Bar();
		$clean_up_head		     = new MySiteName_Clean_Up_Head();
		$insert_figure		     = new MySiteName_Insert_Figure();
		$long_url_spam		     = new MySiteName_Long_URL_Spam();
		$remove_jetpack_bar      = new MySiteName_Remove_Jetpack_Bar();
		$remove_assets			 = new MySiteName_Remove_Unwated_Assets();
		$remove_post_author_url  = new MySiteName_Remove_Post_Author_Url();
		$remove_wp_version			= new MySiteName_Remove_WP_Version();
		
	}
}