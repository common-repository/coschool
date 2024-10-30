<?php
/**
 * All data facing functions
 */
namespace Codexpert\CoSchool\App\Assignment;
use Codexpert\CoSchool\Abstracts\Post_Data;
use Codexpert\CoSchool\Abstracts\DB;
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
 * @subpackage Assignment
 * @author Codexpert <hi@codexpert.io>
 */
class Data extends Post_Data {

    /**
     * @var obj
     */
    public $assignment;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $assignment the assignment
     */
    public function __construct( $assignment ) {
        $this->assignment   = get_post( $assignment );
        parent::__construct( $this->assignment );
    }

    /**
     * Gets associated course ID
     * 
     * @return int|obj the course ID $post_id|$post
     */
    public function get_course() {
        return $this->get( 'course_id' );
    }

    /**
     * Gets the banner
     * 
     * @return string The URL
     */
    public function get_banner( $size = 'coschool-banner' ) {
        return get_the_post_thumbnail( $this->get( 'id' ), $size );
    }

    /**
     * Can a student see this content?
     * 
     * @param int $user_id the student ID
     * 
     * @return bool
     */
    public function is_visible_by( $user_id = null ) {

        if( is_null( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        if( user_can( $user_id, 'edit_post', $this->assignment->ID ) ) {
            return true;
        }

        $course = new Course_Data( $this->get_course() );
        if( $course->get_type() == 'free' ) {
            return true;
        }

        $student_data = new Student_Data( $user_id );
        if( $student_data->has_course( $this->get_course() ) ) {
            return true;
        }

        return false;
    }

    /**
     * If a user has access to this assignment
     * 
     * @param int $user_id The user/visitor ID
     * 
     * @return bool
     */
    public function has_access( $user_id = null ) {

        if( is_null( $user_id ) ) {
            $user_id = get_current_user_id();
        }
        
        $course_data = new Course_Data( $this->get_course() );

        return $course_data->has_access( $user_id );
    }

    /**
     * Gets all submissions to this assignment
     */
    public function get_submissions() {
        global $wpdb;

        $prefix = $wpdb->prefix . coschool_db_prefix();

        return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$prefix}enrollment_progress` WHERE `content_id` = %d", $this->get( 'id' ) ) );
    }

    /**
     * Gets a given stundent's submission to this assignment
     */
    public function get_submission( $student_id ) {
        global $wpdb;

        $prefix = $wpdb->prefix . coschool_db_prefix();

        $student_data = new Student_Data( get_current_user_id() );

        if( is_null( $enrollment = $student_data->get_course_enrollment( $this->get_course() ) ) ) {
            return null;
        }

        return $wpdb->get_row( $wpdb->prepare( "SELECT `reference` FROM `{$prefix}enrollment_progress` WHERE `content_id` = %d AND `enrollment_id` = %d", $this->get( 'id' ), $enrollment->id ) );
    }
}