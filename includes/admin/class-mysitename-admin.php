<?php
/**
 * The MySiteName_Admin class defines all functionality for the admin/back-end/dashboard stuff
 * 
 * @package     MySiteName
 */

/**
 * The MySiteName_Admin class defines all functionality for the admin/back-end/dashboard stuff.
 * 
 * This class registers the stylesheet for use in the admin only, and defines the other functionality specific to the admin dashboard.
 * 
 * @since 1.0.0
 */
class MySiteName_Admin {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		// add_action( 'init', array( $this, 'add_custom_admin_metaboxes' ) );  // Add a call to any functions below
	}
	/**
	 * Enqueue admin-specific styles
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			'mysitename-admin-styles',
			plugin_dir_url( __FILE__ ) . 'css/admin-style.css',
			array(),
			filemtime( plugin_dir_url( __FILE__ ) . 'css/admin-style.css' ),
			FALSE
		);

	}
	/**
     * Description of add_custom_admin_metaboxes function here
     */
	public function add_custom_admin_metaboxes() {
	}

}