<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Enrollment;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Lesson\Data as Lesson_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\Student\Data as Student_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Enrollment
 * @author Codexpert <hi@codexpert.io>
 */
class Data {

    /**
     * @var obj $wpdb
     */
    public $database;

    public function __construct( $enrollment_id = null ) {
        $this->database = new DB;

        if( ! is_null( $enrollment_id ) ) {
            $this->enrollment_id = $enrollment_id;
        }

    }

    public function course_completion( $lesson_id, $student_id ) {
        global $wpdb;
        $lesson_data    = new Lesson_Data( $lesson_id );
        $course_id      = $lesson_data->get_course();
        $course_data    = new Course_Data( $course_id );

        if ( $course_data->is_completed( $student_id ) ) {
            $this->database->update( 'enrollments', [ 'status' => 'completed' ], [ 'course_id' => $course_id ] );
        }
    }

    public function data_completed() {
        $query  = $this->database->select( 'enrollment_progress', 'MAX(`completed_at`) AS timestamp', "`enrollment_id` = {$this->enrollment_id}" );
        return $query[0]->timestamp;
    }

    public function change_enrollment_status() {
        $response = [ 'status' => 0, 'message' => __( 'Something went wrong', 'coschool' ) ];

        if( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ){
            $response['message'] = __( 'Unauthorized', 'coschool' );
            wp_send_json( $response );
        }

        if( ! isset( $_POST['enrollment_id'] ) || ! isset( $_POST['type'] ) || $_POST['type'] == '' || $_POST['enrollment_id'] == '' ){
            $response['message'] = __( 'In valid Data', 'coschool' );
            wp_send_json( $response );
        }

        $enrollment_id      = coschool_sanitize( $_POST['enrollment_id'] );
        $status             = coschool_sanitize( $_POST['type'] );

        update_user_meta( $enrollment_id, 'coschool_user_status', $status );

        $action_btns = [
            'active'    => "<button class='coschool-enrollment-action' data-enrollment='" . esc_attr( $enrollment_id ) . "' data-action='active'>" . __( 'Approve', 'coschool' ) . "</button>",
            'blocked'     => "<button class='coschool-enrollment-action' data-enrollment='" . esc_attr( $enrollment_id ) . "' data-action='blocked'>" . __( 'Block', 'coschool' ) . "</button>",
        ];

        if ( $status && isset( $action_btns[ $status ] ) ) unset( $action_btns[ $status ] );

        $response['status']         = 1;
        $response['message']        = __( 'Status updated', 'coschool' );
        $response['action_btns']    = array_values( $action_btns );

        wp_send_json( $response );
    }
}