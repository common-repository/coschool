<?php
/**
 * All coupon related functions
 */
namespace Codexpert\CoSchool\App\Coupon;
use Codexpert\CoSchool\Helper;

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
class AJAX {

	public function apply_coupon() {

		$response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
			wp_send_json( $response );
		}

		if( null == ( $coupon_id = coschool_get_post_id_by_title(  $_POST['coupon'], 'coupon' ) ) ) {
			wp_send_json( [ 'status' => 0, 'message' => __( 'This coupon doesn\'t exist!', 'coschool' ) ] );
		}

		$coupon_data = new Data( $coupon_id );

		if( ! $coupon_data->is_enabled() ) {
			wp_send_json( [ 'status' => 0, 'message' => __( 'This coupon is disabled!', 'coschool' ) ] );
		}

		if( ! $coupon_data->has_validity() ) {
			wp_send_json( [ 'status' => 0, 'message' => __( 'This coupon was expired!', 'coschool' ) ] );
		}

		$cart = coschool_get_cart_items();
		$coupon_applicable = false;
		foreach ( $cart as $course_id ) {
			if( $coupon_data->applicable_for( $course_id ) ) {
				$coupon_applicable = true;
			}
		}

		// coupon is valid, but doesn't satisfy the conditions
		if( false === $coupon_applicable ) {
			wp_send_json( [ 'status' => 0, 'message' => __( 'This coupon doesn\'t apply to any of the courses in the cart!', 'coschool' ) ] );
		}

		// good to go
		wp_send_json( [ 'status' => 1, 'message' => __( 'Coupon applied successfully!', 'coschool' ), 'coupon' => $coupon_data->get( 'name' ) ] );
	}
}