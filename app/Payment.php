<?php
/**
 * All course related functions
 */
namespace Codexpert\CoSchool\App;
use Codexpert\CoSchool\App\Payment\Provider\Native\PayPal;
use Codexpert\CoSchool\App\Payment\Provider\Test_Payment;
use Codexpert\CoSchool\App\Payment\Cart;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Payment
 * @author Codexpert <hi@codexpert.io>
 */
class Payment {

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
		add_action( 'admin_menu', [ new Payment\Menu, 'register' ] );

		add_action( 'init', [ new Payment\Cart, 'loader' ] );
		add_action( 'coschool-enroll_form_submitted', [ new Payment\Cart, 'process_enrollment' ] );
		add_action( 'coschool_course_enroll', [ new Payment\Cart, 'remove_wishlist_items' ], 10, 2 );

		if( 'native' == coschool_payment_handler() ) {

			$methods = coschool_payment_methods();
			
			// PayPal
			if( in_array( 'paypal', $methods ) ) {
				add_action( 'init', [ new PayPal, 'redirect' ] );
				add_action( 'wp_footer', [ new PayPal, 'enqueue_scripts' ] );
				add_action( 'coschool_payment_form_paypal', [ new PayPal, 'payment_form' ] );
				add_filter( 'coschool_paypal_payment_id', [ new PayPal, 'payment_id' ], 10, 2 );
				add_filter( 'coschool_payment_paypal_config', [ new PayPal, 'settings' ] );
			}
		}
		elseif ( 'test-payment' == coschool_payment_handler() ) {
			add_filter( 'coschool_test-payment_payment_id', [ new Test_Payment, 'payment_id' ], 10, 2 );
		}
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