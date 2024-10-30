<?php
/**
 * All course related functions
 */
namespace Codexpert\CoSchool\App;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Course
 * @author Codexpert <hi@codexpert.io>
 */
class Course {

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
	 * Constructor function
	 */
	public function __construct() {
		add_action( 'init', [ new Course\Post_Type, 'register' ] );
		add_action( 'manage_course_posts_columns', [ new Course\Post_Type, 'add_table_columns' ] );
		add_action( 'manage_course_posts_custom_column', [ new Course\Post_Type, 'add_column_content' ], 10, 2 );
		add_filter( 'post_updated_messages', [ new Course\Post_Type, 'course_updated_message' ] );
		add_filter( 'bulk_post_updated_messages', [ new Course\Post_Type, 'bulk_course_updated_message' ], 10, 2 );
		
		add_action( 'init', [ new Course\Taxonomy, 'register' ] );
		add_action( 'wp_ajax_course-review', [ new Course\AJAX, 'course_review' ] );
		add_action( 'wp_ajax_wishlist', [ new Course\AJAX, 'wishlist' ] );
		
		add_action( 'admin_init', [ new Course\Meta, 'config' ] );
		add_action( 'add_meta_boxes', [ new Course\Meta, 'content' ], 11 );
		add_action( 'save_post_course', [ new Course\Meta, 'save' ], 10, 2 );
		add_action( 'wp_head', [ new Course\Meta, 'show_schema' ] );
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