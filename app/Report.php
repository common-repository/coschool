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
 * @subpackage Report
 * @author Codexpert <hi@codexpert.io>
 */
class Report {

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
		add_action( 'admin_menu', [ new Report\Menu, 'register' ] );
		add_filter( 'coschool-admin-localized', [ new Report\Data, 'filter' ] );
		add_filter( 'coschool-localized-quiz', [ new Report\Data, 'front_admin_report' ] );
		add_filter( 'coschool-localized-quiz', [ new Report\Data, 'front_student_report' ] );
		add_action( 'wp_ajax_periodic-item', [ new Report\AJAX, 'get_filter_items' ] );
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