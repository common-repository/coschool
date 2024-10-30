<?php
/**
 * All quiz related functions
 */
namespace Codexpert\CoSchool\App\Quiz;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Question\Data as Question_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Quiz
 * @author Codexpert <hi@codexpert.io>
 */
class AJAX {

	public function create_new() {

		$response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
			wp_send_json( $response );
		}

		$id = wp_insert_post( array(
		  'post_title'	=> sanitize_text_field( $_POST['name'] ), 
		  'post_type'	=> 'quiz',
		  'post_status'	=> 'publish',
		) );

		$response = [ 'status' => 1, 'message' => __( 'Quiz Created successfully', 'coschool' ), 'item_id' => $id ];
		wp_send_json( $response );
	}

	public function start() {

		$response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
			wp_send_json( $response );
		}

		if( ! isset( $_POST['quiz_id'] ) || $_POST['quiz_id'] == '' ){
			$response['message'] = __( 'Invalid Quiz id!' );
			wp_send_json( $response );
		}
		$questions_html = Helper::get_view( 'questions', 'templates/quiz', [ 'quiz_id' => sanitize_key( $_POST['quiz_id'] ) ] );

		$response = [ 'status' => 1, 'message' => __( 'Quiz Started', 'coschool' ), 'questions_html' => $questions_html ];
		wp_send_json( $response );
	}

	public function submit() {

		$response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
			wp_send_json( $response );
		}

		if( ! isset( $_POST['quiz_id'] ) || $_POST['quiz_id'] == '' ){
			$response['message'] = __( 'Invalid Quiz id!', 'coschool' );
			wp_send_json( $response );
		}

		if ( ! isset( $_POST['answer'] ) ) {
			$response['message'] = __( 'No Answer found', 'coschool' );
			wp_send_json( $response );
		}
		
		$quiz_id 		= coschool_sanitize( $_POST['quiz_id'] );
		$attempt 		= new Attempt( $quiz_id );
		$attempt_id 	= $attempt->store( $_POST );
		$student_data 	= new Student_Data( get_current_user_id() );
		
		if( quiz_attempt_status( $attempt_id ) == 'passed' ){

			$completed 	= $student_data->mark_complete( $quiz_id );

            do_action( 'coschool_quiz_passed', $quiz_id , $student_data->get( 'id' ) );
            $result_html = Helper::get_view( 'passed', 'templates/quiz', [ 'quiz_id' => $quiz_id ] );
		}
		elseif( quiz_attempt_status( $attempt_id ) == 'failed'  ){
			$result_html = Helper::get_view( 'failed', 'templates/quiz', [ 'quiz_id' => $quiz_id ] );
		}
		else {
			$result_html = Helper::get_view( 'result', 'templates/quiz', [ 'quiz_id' => $quiz_id ] );
		}
	
		do_action( 'coschool_content_completed', $quiz_id, $student_data->get( 'id' ) );

		$response = [ 'status' => 1, 'message' => __( 'Quiz Submited', 'coschool' ), 'result_html' => $result_html ];
		wp_send_json( $response );
	}

	/**
	 * @todo
	 */
	public function review_point() {
		$response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
			wp_send_json( $response );
		}

		if ( ! isset( $_POST['row_id'] ) || '' == $_POST['row_id'] ) {
			$response['message'] = __( 'Invalid attempt ID', 'coschool' );
			wp_send_json( $response );
		}

		global $wpdb;

		$table 		= $wpdb->prefix . "coschool_quiz_attempt_answers";
		$point 		= isset( $_POST['point'] ) && $_POST['point'] != '' ? coschool_sanitize( $_POST['point'] ) : 0;
		$row_id 	= coschool_sanitize( $_POST['row_id'] );
		$_attempt 	= $wpdb->get_results( "SELECT * FROM `{$table}` WHERE `id` = {$row_id}" );
		$attempt 	= $_attempt ? $_attempt[0] : [];

		if ( isset( $_POST['type'] ) && $_POST['type'] == 'right' && $point == 0 && $attempt ) {
			$question_data 	= new Question_Data( $attempt->question );
			$point 			= $question_data->get_point();
		}
		else if( isset( $_POST['type'] ) && $_POST['type'] == 'wrong' ){
			$point = 0;
		}
		$wpdb->update( $table, [ 'points' => $point ], [ 'id' => $row_id ] );

		if ( $attempt && $point != 0 ) {
			$attempt_id 	= $attempt->attempt_id;
			$question_data 	= new Question_Data( $attempt->question );
			$quiz_id 		= $question_data->get( 'quiz_id' );
			$attempt 		= new Attempt( $quiz_id );
			$student 		= $attempt->get_student_id( $attempt_id );
			$is_passed 		= $attempt->quiz_data->is_passed( (int)$student );

			if( $is_passed ) {
				$student_data 	= new Student_Data( $student );
				$completed 		= $student_data->mark_complete( $quiz_id );

				$wpdb->update( $wpdb->prefix . "coschool_quiz_attempts", [ 'status' => 'passed' ], [ 'id' => $attempt_id  ] );

                do_action( 'coschool_quiz_passed', $quiz_id, $student_data->get( 'id' ) );
			}
			else {
				$wpdb->update( $wpdb->prefix . "coschool_quiz_attempts", [ 'status' => 'failed' ], [ 'id' => $attempt_id  ] );
			}
		}

		$response['point'] 		= number_format((float)$point, 2, '.', '');
		$response['status'] 	= 1;
		$response['message'] 	= __( 'Point Updated', 'coschool' );
		wp_send_json( $response );
	}

	/**
	 * @todo
	 */
	public function attempt_feedback() {
		$response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
			wp_send_json( $response );
		}

		if ( ! isset( $_POST['row_id'] ) || '' == $_POST['row_id'] ) {
			$response['message'] = __( 'Invalid attempt ID', 'coschool' );
			wp_send_json( $response );
		}

		global $wpdb;
		$table 		= $wpdb->prefix . "coschool_quiz_attempt_answers";
		$feedback 	= isset( $_POST['feedback'] ) && $_POST['feedback'] != '' ? coschool_sanitize( $_POST['feedback'] ) : '';
		$row_id 	= coschool_sanitize( $_POST['row_id'] );

		$wpdb->update( $table, [ 'feedback' => $feedback ], [ 'id' => $row_id ] );

		$response['feedback'] 	= $feedback;
		$response['status'] 	= 1;
		$response['message'] 	= __( 'Feedback Updated', 'coschool' );
		wp_send_json( $response );
	}
}