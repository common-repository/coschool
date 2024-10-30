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
 * @subpackage Coupon
 * @author Codexpert <hi@codexpert.io>
 */
class Coupon {

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
		if( 'native' != coschool_payment_handler() ) return;
		
		add_action( 'init', [ new Coupon\Post_Type, 'register' ], 11 );
		add_action( 'manage_coupon_posts_columns', [ new Coupon\Post_Type, 'add_table_columns' ] );
		add_action( 'manage_coupon_posts_custom_column', [ new Coupon\Post_Type, 'add_column_content' ], 10, 2 );
		add_filter( 'post_updated_messages', [ new Coupon\Post_Type, 'coupon_updated_message' ] );
		add_filter( 'bulk_post_updated_messages', [ new Coupon\Post_Type, 'bulk_coupon_updated_message' ], 10, 2 );

		add_action( 'wp_ajax_coschool-apply-coupon', [ new Coupon\AJAX, 'apply_coupon' ] );
		add_action( 'wp_ajax_nopriv_coschool-apply-coupon', [ new Coupon\AJAX, 'apply_coupon' ] );

		add_action( 'admin_init', [ new Coupon\Meta, 'config' ] );
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