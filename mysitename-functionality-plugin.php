<?php
/**
 * 
 * The file responsible for starting the MySiteName Functionality plugin
 * 
 * This plugin adds a bunch of functionality to a WordPress install.
 * This particular file is responsible for including the necessary dependencies and starting the plugin.
 * 
 * @package     MySiteName
 *
 * @wordpress-plugin
 * Plugin Name:       MySiteName Functionality
 * Plugin URI:        https://github.com/davemoz/WP-functionality-plugin
 * Description:       Custom WordPress functionality plugin.
 * Version:           1.0.0
 * Author:            Dave Mozdzanowski
 * Author URI:        http://davemoz.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:				mysitename-functionality-locale
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
	die;
}

// Defines the encompassing main class of the plugin
if( !class_exists( 'MySiteName_Functionality' ) ) {
	class MySiteName_Functionality {

		/**
		 * A reference to the admin loader class that coordinates the hooks and callbacks for the admin portion of the plugin
		 * 
		 * @access	protected
		 * @var			MySiteName_Admin_Loader $adminloader Manages hooks between the WordPress admin hooks and the callback functions.
		 */
		protected $adminloader;

		/**
		 * Instance of the class
		 *
		 * @since 1.0.0
		 * @var Instance of MySiteName_Functionality class
		 */
		private static $instance;

		/**
		 * Instance of the plugin
		 *
		 * @since 1.0.0
		 * @static
		 * @staticvar array $instance
		 * @return Instance
		 */
		public static function instance() {
			if ( !isset( self::$instance ) && ! ( self::$instance instanceof MySiteName_Functionality ) ) {
				self::$instance = new MySiteName_Functionality;
				self::$instance->define_constants();
				add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
				self::$instance->includes();
				self::$instance->define_admin_hooks();
				self::$instance->init = new MySiteName_Init();
			}
		return self::$instance;
		}

		/**
		 * Define the plugin constants
		 *
		 * @since  1.0.0
		 * @access private
		 * @return void
		 */
		private function define_constants() {
			// Plugin Version
			if ( ! defined( 'MYSITENAME_VERSION' ) ) {
				define( 'MYSITENAME_VERSION', '1.0.0' );
			}
			// Prefix
			if ( ! defined( 'MYSITENAME_PREFIX' ) ) {
				define( 'MYSITENAME_PREFIX', 'mysitename_' );
			}
			// Textdomain
			if ( ! defined( 'MYSITENAME_TEXTDOMAIN' ) ) {
				define( 'MYSITENAME_TEXTDOMAIN', 'mysitename' );
			}
			// Plugin Options
			if ( ! defined( 'MYSITENAME_OPTIONS' ) ) {
				define( 'MYSITENAME_OPTIONS', 'mysitename-options' );
			}
			// Plugin Directory
			if ( ! defined( 'MYSITENAME_PLUGIN_DIR' ) ) {
				define( 'MYSITENAME_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}
			// Plugin URL
			if ( ! defined( 'MYSITENAME_PLUGIN_URL' ) ) {
				define( 'MYSITENAME_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}
			// Plugin Root File
			if ( ! defined( 'MYSITENAME_PLUGIN_FILE' ) ) {
				define( 'MYSITENAME_PLUGIN_FILE', __FILE__ );
			}
		}

		/**
		 * Load the required files
		 *
		 * @since  1.0.0
		 * @access private
		 * @return void
		 */
		private function includes() {
			$includes_path = plugin_dir_path( __FILE__ ) . 'includes/';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/admin/class-mysitename-admin.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/admin/class-mysitename-admin-loader.php';
			$this->adminloader = new MySiteName_Admin_Loader();

			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-custom-nav-walker.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-woocommerce.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-register-post-types.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-register-taxonomies.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-remove-admin-bar.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-clean-up-head.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-insert-figure.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-long-url-spam.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-remove-jetpack-bar.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-remove-unwanted-assets.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-remove-post-author-url.php';
			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-custom-pagi.php';


			require_once MYSITENAME_PLUGIN_DIR . 'includes/class-mysitename-init.php';
		}

		/**
		 * Defines the hooks and callback functions that are used for setting up the plugin's stylesheets
		 * 
		 * This function relies on the MySiteName_Admin class and the MySiteName_Admin_Loader class property.
		 * 
		 * @access private
		 */
		private function define_admin_hooks() {

			$admin = new MySiteName_Admin();
			$this->adminloader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles');

		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function load_textdomain() {
			$mysitename_lang_dir = dirname( plugin_basename( MYSITENAME_PLUGIN_FILE ) ) . '/languages/';
			$mysitename_lang_dir = apply_filters( 'MySiteName_lang_dir', $mysitename_lang_dir );

			$locale = apply_filters( 'plugin_locale',  get_locale(), MYSITENAME_TEXTDOMAIN );
			$mofile = sprintf( '%1$s-%2$s.mo', MYSITENAME_TEXTDOMAIN, $locale );

			$mofile_local  = $mysitename_lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/edd/' . $mofile;

			if ( file_exists( $mofile_local ) ) {
				load_textdomain( MYSITENAME_TEXTDOMAIN, $mofile_local );
			} else {
				load_plugin_textdomain( MYSITENAME_TEXTDOMAIN, false, $mysitename_lang_dir );
			}
		}

		/**
		 * Throw error on object clone
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', MYSITENAME_TEXTDOMAIN ), '1.6' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since  1.0.0
		 * @access public
		 * @return void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', MYSITENAME_TEXTDOMAIN ), '1.6' );
		}

	}
}
/**
 * Return the instance
 *
 * @since 1.0.0
 * @return object The Safety Links instance
 */
function MySiteName_Functionality_Run() {
	return MySiteName_Functionality::instance();
}
MySiteName_Functionality_Run();