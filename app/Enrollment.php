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
 * @subpackage Enrollment
 * @author Codexpert <hi@codexpert.io>
 */
class Enrollment {

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
		add_action( 'admin_menu', [ new Enrollment\Menu, 'register' ] );
		add_action( 'coschool_content_completed', [ new Enrollment\Data, 'course_completion' ], 10, 2 );
		add_action( 'wp_ajax_coschool-enrollment-approval', [ new Enrollment\Data, 'change_enrollment_status' ] );
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