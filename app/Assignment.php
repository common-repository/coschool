<?php
namespace Codexpert\CoSchool\App;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Assignment {
	
	/**
	 * Plugin instance
	 * 
	 * @access private
	 * 
	 * @var Plugin
	 */
	private static $_instance;

	public function __construct() {

		/**
		 * Admin
		 */
		add_filter( 'coschool_content_tabs', [ new Assignment\Admin, 'content_tabs' ] );

		/**
		 * Admin
		 */
		add_action( 'wp_enqueue_scripts', [ new Assignment\Front, 'enqueue_scripts' ] );

		/**
		 * Common
		 */
		add_action( 'init', [ new Assignment\Common, 'register_cpt' ], 11 );
		add_action( 'manage_assignment_posts_columns', [ new Assignment\Common, 'add_table_columns' ] );
		add_action( 'manage_assignment_posts_custom_column', [ new Assignment\Common, 'add_column_content' ], 10, 2 );
		add_action( 'admin_menu', [ new Assignment\Common, 'add_menu' ], 11 );
		add_action( 'submenu_file', [ new Assignment\Common, 'highlight_menu' ] );
		add_filter( 'post_type_link', [ new Assignment\Common, 'permalink' ], 10, 3 );
		add_filter( 'the_content', [ new Assignment\Common, 'filter_content' ] );
		add_filter( 'post_updated_messages', [ new Assignment\Common, 'assignment_updated_message' ] );
		add_filter( 'bulk_post_updated_messages', [ new Assignment\Common, 'bulk_assignment_updated_message' ], 10, 2 );

		/**
		 * AJAX
		 */
		add_action( 'wp_ajax_create-new-assignment', [ new Assignment\AJAX, 'create_new_assignment' ] );
		add_action( 'wp_ajax_assignment-remove', [ new Assignment\AJAX, 'assignment_remove' ] );
		add_action( 'wp_ajax_coschool-submit-assignment', [ new Assignment\AJAX, 'submit_assignment' ] );

		/**
		 * Meta
		 */
		add_action( 'admin_init', [ new Assignment\Meta, 'config' ], 11 );
		add_action( 'cx-settings-before-fields', [ new Assignment\Meta, 'view_course' ] );

		/**
		 * Template
		 */
		add_action( 'plugins_loaded', [ new Assignment\Template, 'init' ] );
	}

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