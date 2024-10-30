<?php
/**
 * Plugin Name:			CoSchool LMS
 * Description:			A complete LMS toolkit. Simple and complete.
 * Plugin URI:			https://codexpert.io/coschool
 * Author:				Codexpert, Inc
 * Author URI:			https://codexpert.io
 * Version:				1.2
 * Requires at least:	5.0
 * Requires PHP:		7.4
 * License: 			GPL v2 or later
 * License URI:			https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:			coschool
 * Domain Path:			/languages
 */

/**
 * CoSchool is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * CoSchool is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

namespace Codexpert\CoSchool;
use Codexpert\Plugin\Widget;
use Codexpert\Plugin\Notice;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main class for the plugin
 * @package Plugin
 * @author Codexpert <hi@codexpert.io>
 */
final class Plugin {
	

	public $plugin;
	/**
	 * Plugin instance
	 * 
	 * @access private
	 * 
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * The constructor method
	 * 
	 * @access private
	 * 
	 * @since 0.9
	 */
	private function __construct() {
		/**
		 * Includes required files
		 */
		$this->include();

		/**
		 * Defines contants
		 */
		$this->define();

		/**
		 * Run actual hooks
		 */
		$this->hook();
	}

	/**
	 * Includes files
	 * 
	 * @access private
	 * 
	 * @uses composer
	 * @uses psr-4
	 */
	private function include() {
		require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );
	}

	/**
	 * Define variables and constants
	 * 
	 * @access private
	 * 
	 * @uses get_plugin_data
	 * @uses plugin_basename
	 */
	private function define() {

		/**
		 * Define some constants
		 * 
		 * @since 0.9
		 */
		define( 'COSCHOOL', __FILE__ );
		define( 'COSCHOOL_DIR', dirname( COSCHOOL ) );
		define( 'COSCHOOL_ASSET', plugins_url( 'assets', COSCHOOL ) );
		define( 'COSCHOOL_DEBUG', apply_filters( 'coschool_debug', true ) );

		/**
		 * The plugin data
		 * 
		 * @since 0.9
		 * @var $plugin
		 */
		$this->plugin				= get_plugin_data( COSCHOOL );
		$this->plugin['basename']	= plugin_basename( COSCHOOL );
		$this->plugin['file']		= COSCHOOL;
		$this->plugin['assets']		= COSCHOOL_ASSET;
		$this->plugin['server']		= apply_filters( 'coschool_server', 'https://my.pluggable.io' );
		$this->plugin['min_php']	= '5.6';
		$this->plugin['min_wp']		= '4.0';
		$this->plugin['doc_id']		= 19;
		$this->plugin['depends']	= apply_filters( 'coschool_depends', [] );

		if( ! in_array( ( $handler 	= coschool_payment_handler() ), [ 'native', 'test-payment' ] ) ) {
			$this->plugin['depends'][ "{$handler}/{$handler}.php" ] = coschool_dependencies( $handler );
		}

		/**
		 * Set a global variable
		 * 
		 * @global $coschool
		 */
		global $coschool;
		$coschool = $this->plugin;
	}

	/**
	 * Hooks
	 * 
	 * @access private
	 * 
	 * Executes main plugin features
	 *
	 * To add an action, use $instance->action()
	 * To apply a filter, use $instance->filter()
	 * To register a shortcode, use $instance->register()
	 * To add a hook for logged in users, use $instance->priv()
	 * To add a hook for non-logged in users, use $instance->nopriv()
	 * 
	 * @return void
	 */
	private function hook() {

		if( is_admin() ) :

			/**
			 * Admin facing hooks
			 */
			$admin = new Admin( $this->plugin );
			$admin->activate( 'install' );
			// $admin->deactivate( 'uninstall' );
			$admin->action( 'plugins_loaded', 'i18n' );
			$admin->action( 'admin_menu', 'add_menu', 10 );
			$admin->action( 'admin_head', 'menu_position' );
			$admin->action( 'wp_before_admin_bar_render', 'add_admin_bar_menu' );
			$admin->filter( "plugin_action_links_{$this->plugin['basename']}", 'action_links' );
			$admin->filter( 'plugin_row_meta', 'plugin_row_meta', 10, 2 );
			$admin->action( 'save_post', 'update_cache', 10, 3 );
			$admin->action( 'admin_footer_text', 'footer_text' );
			$admin->action( 'cx-settings-saved', 'settings_saved' );
			$admin->action( 'admin_footer', 'admin_footer' );
			$admin->filter( 'wp_default_editor', 'editor', 10, 1 );
			$admin->filter( 'manage_edit-comments_columns', 'reviews_comment' );
			$admin->filter( 'manage_comments_custom_column', 'rating_column', 10, 2 );
			$admin->action( 'admin_notices', 'admin_notices' );

			/**
			 * Settings related hooks
			 */
			$settings = new Settings( $this->plugin );
			$settings->action( 'plugins_loaded', 'init_menu', 99 );
			$settings->action( 'cx-settings-after-field', 'builder', 10, 2 );
			$settings->action( 'cx-settings-reset', 'clear_certificate' );

			/**
			 * Registers a widget in the wp-admin/ screen
			 * 
			 * @package Codexpert\Plugin
			 * 
			 * @author Codexpert <hi@codexpert.io>
			 */
			$widget = new Widget( $this->plugin );


			/**
			 * Renders different norices
			 * 
			 * @package Codexpert\Plugin
			 * 
			 * @author Codexpert <hi@codexpert.io>
			 */
			$notice = new Notice( $this->plugin );


		else : // !is_admin() ?

			/**
			 * Front facing hooks
			 */
			$front = new Front( $this->plugin );
			$front->action( 'wp_head', 'head' );
			$front->action( 'admin_bar_menu', 'add_admin_bar', 70 );
			$front->filter( 'body_class', 'body_class' );
			$front->action( 'wp_footer', 'modal' );
			$front->filter( 'login_form_top', 'login_form_top', 10, 2 );
			$front->filter( 'login_form_bottom', 'login_form_bottom', 10, 2 );
			$front->filter( 'get_avatar_url', 'avatar_url', 10, 3 );
			$front->action( 'after_setup_theme', 'remove_admin_bar');

			/**
			 * Short Code related hooks
			 */
			$shortcode = new Shortcode( $this->plugin );
			$shortcode->register( 'coschool_enroll', 'enroll' );
			$shortcode->register( 'coschool_dashboard', 'dashboard' );
			$shortcode->register( 'coschool_login', 'login' );
			$shortcode->register( 'coschool_courses', 'courses' );

			/**
			 * Custom REST API related hooks
			 */
			$api = new API( $this->plugin );
			$api->action( 'rest_api_init', 'register_endpoints' );

		endif;

		/**
		 * Both Admin & Front facing hooks
		 */
		$common = new Common( $this->plugin );		
		$common->filter( 'init', 'rewrite_rule' );
		$common->filter( 'query_vars', 'query_vars' );
		$common->action( 'wp_login', 'user_login', 10, 2 );


		/**
		 * Css and js enqueue
		 */
		$assets = new Assets( $this->plugin );
		$assets->action( 'admin_enqueue_scripts', 'admin_css' );
		$assets->action( 'admin_enqueue_scripts', 'admin_js' );
		$assets->action( 'wp_enqueue_scripts', 'front_fonts' );
		$assets->action( 'wp_enqueue_scripts', 'front_css' );
		$assets->action( 'wp_enqueue_scripts', 'front_js' );
		$assets->action( 'after_setup_theme', 'register_thumbnails' );
		$assets->action( 'ajax_query_attachments_args', 'show_current_user_attachments' );

		/**
		 * Cron facing hooks
		 */
		$cron = new Cron( $this->plugin );
		$cron->activate( 'install' );
		$cron->deactivate( 'uninstall' );
		$cron->action( 'codexpert-daily', 'daily' );
		$cron->action( 'plugins_loaded', 'initial_calls' );

		/**
		 * AJAX related hooks
		 */
		$ajax = new AJAX( $this->plugin );
		$ajax->priv( 'coschool-user_switcher', 'user_switcher' );

		/**
		 * The App loader
		 * 
		 * Loads actual app
		 */
		$app = new App( $this->plugin );
		$app->action( 'plugins_loaded', 'load' );

		/**
		 * Template init
		 */
		$template = new Template( $this->plugin );
		$template->action( 'plugins_loaded', 'init' );

		/**
		 * Email init
		 */
		$email = new Email( $this->plugin );
		$email->action( 'plugins_loaded', 'init' );
	}

	/**
	 * Cloning is forbidden.
	 * 
	 * @access public
	 */
	public function __clone() { }

	/**
	 * Unserializing instances of this class is forbidden.
	 * 
	 * @access public
	 */
	public function __wakeup() { }

	/**
	 * Instantiate the plugin
	 * 
	 * @access public
	 * 
	 * @return $_instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}

Plugin::instance();