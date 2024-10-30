<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Lesson;
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

	public function create_new_lesson() {

		$response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
			wp_send_json( $response );
		}

		$id = wp_insert_post( array(
		  'post_title'	=> coschool_sanitize( $_POST['name'] ), 
		  'post_type'	=> 'lesson',
		  'post_status'	=> 'publish',
		) );

		$response = [ 'status' => 1, 'message' => __( 'Lesson Created successfully', 'coschool' ), 'item_id' => $id ];
		wp_send_json( $response );
	}

	public function mark_complete() {

		$response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
			wp_send_json( $response );
		}

		$student_data = new Student_Data( get_current_user_id() );
		$student_data->mark_complete( $lesson_id = coschool_sanitize( $_POST['content_id'] ) );

		do_action( 'coschool_content_completed', $lesson_id, $student_data->get( 'id' ) );

		wp_send_json( [ 'status' => 1, 'message' => __( 'Content marked as completed!', 'coschool' ) ] );
	}
}