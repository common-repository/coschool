<?php
/**
 * All lesson related functions
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
 * @subpackage Lesson
 * @author Codexpert <hi@codexpert.io>
 */
class Lesson {

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
		add_action( 'init', [ new Lesson\Post_Type, 'register' ] );
		add_action( 'manage_lesson_posts_columns', [ new Lesson\Post_Type, 'add_table_columns' ] );
		add_action( 'manage_lesson_posts_custom_column', [ new Lesson\Post_Type, 'add_column_content' ], 10, 2 );
		add_filter( 'post_type_link', [ new Lesson\Post_Type, 'permalink' ], 10, 3 );
		add_filter( 'the_content', [ new Lesson\Post_Type, 'filter_content' ] );
		add_filter( 'post_updated_messages', [ new Lesson\Post_Type, 'lesson_updated_message' ] );
		add_filter( 'bulk_post_updated_messages', [ new Lesson\Post_Type, 'bulk_lesson_updated_message' ], 10, 2 );

		add_action( 'wp_ajax_create-new-lesson', [ new Lesson\AJAX, 'create_new_lesson' ] );
		add_action( 'wp_ajax_mark-complete', [ new Lesson\AJAX, 'mark_complete' ] );

		add_action( 'admin_init', [ new Lesson\Meta, 'config' ], 11 );
		add_action( 'cx-settings-before-fields', [ new Lesson\Meta, 'view_course' ] );
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