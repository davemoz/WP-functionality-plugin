<?php
/**
 * Remove WP version number throughout site
 * 
 * @package     MySiteName
 * @subpackage  MySiteName/includes
 * @copyright   Copyright (c) 2014, Jason Witt
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 * @author      Jason Witt <contact@jawittdesigns.com>
 */
class MySiteName_Remove_WP_Version {
	/**
	 * Initialize the class
	 */
	public function __construct() {
		add_filter( 'the_generator', array( $this, 'mysitename_remove_wp_version' ) );
	}
	/**
     * Remove WP generated content from the head
     *
     * @since  1.0.0
     * @access private
     * @return void
     */
	public function mysitename_remove_wp_version() {
		return '';
	}
}