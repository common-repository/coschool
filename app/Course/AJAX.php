<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Course;
use Codexpert\CoSchool\App\Student\Data as Student_Data;

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
class AJAX {
	public function course_review()	{
		$response = [];

        if( !wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
            $response['status']		= 0;
            $response['message'] 	= __( 'Unauthorized!', 'coschool' );
            wp_send_json( $response );
        }

        $user       = wp_get_current_user();

        $rating 	= isset( $_POST['rating'] ) ? coschool_sanitize( $_POST['rating'] ) : 0;
        $comment 	= isset( $_POST['comment'] ) ? coschool_sanitize( $_POST['comment'] ) : '';
        $course_id 	= isset( $_POST['course_id'] ) ? coschool_sanitize( $_POST['course_id'] ) : 0;

        $data = array(
            'comment_post_ID'      => $course_id,
            'comment_content'      => $comment,
            // 'comment_parent'       => $comment_parent,
            'user_id'              => $user->ID,
            'comment_author'       => $user->user_login,
            'comment_author_email' => $user->user_email,
            'comment_author_url'   => $user->user_url,
        );
 
        $comment_id = wp_insert_comment( $data );

        $student_data = new Student_Data( $user->ID );

        $student_data->set( "reviewed_{$course_id}", 1 );

        if ( !empty( $rating ) ) {
        	add_comment_meta( $comment_id, 'rating', $rating );
        }

        $response['status'] 		= 1;
		$response['message']    	= __( 'Successfully review submit', 'coschool' );
		wp_send_json( $response );
	}

    public function wishlist() {
        $response = [];

        if( !wp_verify_nonce( $_POST['_nonce'], 'coschool' ) ) {
            $response['status']     = 0;
            $response['message']    = __( 'Unauthorized!', 'coschool' );
            wp_send_json( $response );
        }

        $course_id  = coschool_sanitize( $_POST['course_id'] );

        // @todo should we mark this complete immediately?
        $student_data = new Student_Data( get_current_user_id() );

        if ( isset( $_POST['added'] ) && $_POST['added'] == 1 ) {
            $student_data->remove_from_wishlist( $course_id );

            $response['status']     = 1;
            $response['action']     = 'remove';
            $response['message']    = __( 'Remove to wishlist', 'coschool' );
        }
        else {
            $student_data->add_to_wishlist( $course_id );

            $response['status']     = 1;
            $response['action']     = 'added';
            $response['message']    = __( 'Added to wishlist', 'coschool' );
        }

        wp_send_json( $response );
    }
}