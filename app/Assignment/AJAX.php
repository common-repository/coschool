<?php
/**
 * All AJAX facing functions
 */
namespace Codexpert\CoSchool\App\Assignment;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\App\Student\Data as Student_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage AJAX
 * @author Codexpert <hi@codexpert.io>
 */
class AJAX {

	/**
	 * Constructor function
	 */
	public function __construct() {}

    public function create_new_assignment() {

        $response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
            wp_send_json( $response );
        }

        $id = wp_insert_post( array(
          'post_title'  => sanitize_text_field( $_POST['name'] ), 
          'post_type'   => 'assignment',
          'post_status' => 'publish',
        ) );

        $response = [ 'status' => 1, 'message' => __( 'Assignment Created successfully', 'coschool' ), 'item_id' => $id ];
        wp_send_json( $response );
    }

    public function submit_assignment() {
        $response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
            wp_send_json( $response );
        }

        $assignment_id  = coschool_sanitize( $_POST['assignment_id'] );
        $attachments    = serialize( array_map( 'coschool_sanitize', $_POST['attachment_id'] ) );

        // @todo should we mark this complete immediately?
        $student_data   = new Student_Data( get_current_user_id() );
        $completed      = $student_data->mark_complete( $assignment_id, $attachments );

        do_action( 'coschool_content_completed', $assignment_id, $student_data->get( 'id' ) );
        
        $response = [ 'status' => 1, 'message' => __( 'Assignment submission successfully', 'coschool' ) ];

        wp_send_json( $response );
    }

    public function assignment_remove() {
        $response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];

        if ( ! wp_verify_nonce( $_POST['_nonce'], 'coschool' ) ) {
            wp_send_json( $response );
        }

        $assignment_id = isset( $_POST['attachment_id'] ) ? $_POST['attachment_id'] : 0;

        wp_delete_attachment( $assignment_id, true );

        $response = [ 'status' => 1, 'message' => __( 'Assignment remove successfully', 'coschool' ) ];
        wp_send_json( $response );
    }
}