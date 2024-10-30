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
 * @subpackage Instructor
 * @author Codexpert <hi@codexpert.io>
 */
class Instructor {

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
		add_action( 'init', [ new Instructor\User_Role, 'register' ] );

		add_action( 'wp_ajax_update-profile', [ new Instructor\AJAX, 'update_profile' ] );
		add_action( 'wp_ajax_review-reply', [ new Instructor\AJAX, 'review_reply' ] );
		add_action( 'wp_ajax_create-course', [ new Instructor\AJAX, 'create_course' ] );
		add_action( 'wp_ajax_edit-course', [ new Instructor\AJAX, 'edit_lesson' ] );
		add_action( 'wp_ajax_edit-quiz', [ new Instructor\AJAX, 'edit_quiz' ] );
		add_action( 'wp_ajax_edit-assignment', [ new Instructor\AJAX, 'edit_assignment' ] );
		add_action( 'wp_ajax_create-lesson', [ new Instructor\AJAX, 'create_lesson' ] );
		// add_action( 'wp_ajax_coschool-delete-post', [ new Instructor\AJAX, 'coschool_delete_post' ] );
		add_action( 'wp_ajax_coschool-delete-course', [ new Instructor\AJAX, 'delete_course' ] );
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