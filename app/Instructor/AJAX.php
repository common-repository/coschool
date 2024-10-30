<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Instructor;
use Codexpert\CoSchool\Helper;

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
class AJAX {
	
    public function update_profile(){

        $response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
            wp_send_json( $response );
        }

        $pass1          = isset( $_POST['pass1'] ) ? sanitize_text_field( $_POST['pass1'] ) : '';
        $pass2          = isset( $_POST['pass2'] ) ? sanitize_text_field( $_POST['pass2'] ) : '';
        $first_name     = isset( $_POST['first_name'] ) ? sanitize_text_field( $_POST['first_name'] ) : '';
        $last_name      = isset( $_POST['last_name'] ) ? sanitize_text_field( $_POST['last_name'] ) : '';
        $email          = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
        $phone_number   = isset( $_POST['phone_number'] ) ? sanitize_text_field( $_POST['phone_number'] ) : '';
        $image_url      = isset( $_POST['image_url'] ) ? sanitize_url( $_POST['image_url'] ) : '';
        $sign_url       = isset( $_POST['sign_url'] ) ? sanitize_url( $_POST['sign_url'] ) : '';

        if ( $pass1 != $pass2 ) {
            $response = [ 'status' => 0, 'message' => __( 'Passwords don\'t match', 'coschool' ) ];
            wp_send_json( $response );
        }

        $data = [
            'ID'            => get_current_user_id(),
            'user_pass'     => $pass1,
            'user_email'    => $email,
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'display_name'  => $first_name . ' ' . $last_name,
            'meta_input'    => [ 'phone_number' => $phone_number, '_coschool_avatar' => $image_url,'_coschool_sign' => $sign_url, ],
        ];
        $user_data = wp_update_user( $data );

        if ( is_wp_error( $user_data ) ) {
            $response = [ 'status' => 0, 'message' => __( 'Can\'t updated', 'coschool' ) ];
            wp_send_json( $response );
        } 
        else {
            $response = [ 'status' => 1, 'message' => __( 'Profile updated', 'coschool' ) ];
        }
        wp_send_json( $response );
    }
}