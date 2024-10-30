<?php
/**
 * All shortcode related functions
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Shortcode
 * @author Codexpert <hi@codexpert.io>
 */
class Shortcode extends Base {

	public $plugin;

	public $slug;

	public $version;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin		= $plugin;
		$this->slug			= $this->plugin['TextDomain'];
		$this->version		= $this->plugin['Version'];
	}

	/**
	 * Course enrollment form
	 * 
	 * @since 0.9
	 */
	public function enroll() {
		// if native payment is not enabled, abort
		if( 'native' != coschool_payment_handler() && 'test-payment' != coschool_payment_handler()  ) {
			return __( 'Something went wrong!', 'coschool' );
		}

		return Helper::get_view( 'enroll', 'templates/shortcodes' );
	}

	/**
	 * Login form
	 * 
	 * @since 0.9
	 */
	public function login() {
		return Helper::get_view( 'login', 'templates/shortcodes/authentication' );
	}

	/**
	 * The dashboard for instructors and students
	 * 
	 * @since 0.9
	 */
	public function dashboard(){
		$user_id = get_current_user_id();

		// message for non-logged in users
		if ( 0 == $user_id ) {
			return do_shortcode( '[coschool_login]' );
		}

		$status = get_user_meta( $user_id, 'coschool_user_status', true );

		if ( $status && $status != 'active' ) {
			echo "<p id='coschool-user-status-notice'>". sprintf( __( 'Your account is %s.', 'coschool' ), $status ) ."</p>";
			return;
		}

		$content = Helper::get_view( "student-dashboard", 'templates/shortcodes' );

		return  apply_filters( "coschool_dashboard_content", "{$content}" );
	}

	public function courses( $attr ){
		$args = shortcode_atts( array(
				'count' 		=> 6,
				'type'			=> '',
				'instructor'	=> '',
				'category'		=> '',
		), $attr );
		return Helper::get_view( 'courses', 'templates/shortcodes', $args );

	}
}