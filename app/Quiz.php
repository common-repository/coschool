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
 * @subpackage Quiz
 * @author Codexpert <hi@codexpert.io>
 */
class Quiz {

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
		add_action( 'init', [ new Quiz\Post_Type, 'register' ] );
		add_action( 'manage_quiz_posts_columns', [ new Quiz\Post_Type, 'add_table_columns' ] );
		add_action( 'manage_quiz_posts_custom_column', [ new Quiz\Post_Type, 'add_column_content' ], 10, 2 );
		add_filter( 'post_type_link', [ new Quiz\Post_Type, 'permalink' ], 10, 3 );
		add_filter( 'the_content', [ new Quiz\Post_Type, 'filter_content' ] );
		add_filter( 'post_updated_messages', [ new Quiz\Post_Type, 'quiz_updated_message' ] );
		add_filter( 'bulk_post_updated_messages', [ new Quiz\Post_Type, 'bulk_quiz_updated_message' ], 10, 2 );

		add_action( 'wp_ajax_create-new-quiz', [ new Quiz\AJAX, 'create_new' ] );
		add_action( 'wp_ajax_coschool-start-quiz', [ new Quiz\AJAX, 'start' ] );
		add_action( 'wp_ajax_coschool-quiz-submit', [ new Quiz\AJAX, 'submit' ] );
		add_action( 'wp_ajax_coschool-attempt-point', [ new Quiz\AJAX, 'review_point' ] );
		add_action( 'wp_ajax_coschool-attempt-feedback', [ new Quiz\AJAX, 'attempt_feedback' ] );

		add_action( 'add_meta_boxes', [ new Quiz\Meta, 'question' ], 11 );
		add_action( 'admin_init', [ new Quiz\Meta, 'config' ], 11 );
		add_action( 'cx-settings-before-fields', [ new Quiz\Meta, 'view_course' ] );
		add_action( 'save_post_quiz', [ new Quiz\Meta, 'save' ], 10, 2 );
		
		add_action( 'admin_menu', [ new Quiz\Post_Type, 'add_menu' ] );
		add_action( 'submenu_file', [ new Quiz\Post_Type, 'highlight_menu' ] );
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