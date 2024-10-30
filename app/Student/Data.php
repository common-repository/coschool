<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Student;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\Abstracts\User_Data;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\App\Course\Data as Course_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Student
 * @author Codexpert <hi@codexpert.io>
 */
class Data extends User_Data {

    /**
     * @var obj
     */
    public $student;

    /**
     * @var obj
     */
    public $database;


    public $db_prefix;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $student the student
     */
    public function __construct( $student ) {
        $this->student = get_userdata( $student );
        parent::__construct( $this->student );

        $this->database     = new DB();

        $this->db_prefix    = coschool_db_prefix();
    }

    public function get_courses( $status = null ) {
        global $wpdb;

        if( ! is_a( $this->student, 'WP_User' ) ) {
            return [];
        }

        if( is_null( $status ) ) {
            $rows = $wpdb->get_results( $wpdb->prepare( "SELECT `course_id` FROM `{$wpdb->prefix}{$this->db_prefix}enrollments` WHERE `status` != 'pending' AND `student` = %d", $this->student->ID ) );
        }
        else {
            $rows = $wpdb->get_results( $wpdb->prepare( "SELECT `course_id` FROM `{$wpdb->prefix}{$this->db_prefix}enrollments` WHERE `status` = '%s' AND `student` = %d", $status, $this->student->ID ) );
        }

        $courses = [];
        foreach ( $rows as $row ) {
            $courses[] = $row->course_id;
        }

        return array_unique( $courses );
    }

    /**
     * Enrollment information of a given course
     * 
     * @param int $course_id
     * 
     * @return null|obj
     */
    public function get_course_enrollment( $course_id ) {
        global $wpdb;

        $enrollment = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}enrollments` WHERE `status` != 'pending' AND `student` = %d AND `course_id` = %d", $this->get( 'id' ), $course_id ) );

        return $enrollment;
    }

    public function has_course( $course_id ) {
        $courses = $this->get_courses();
        return in_array( $course_id, $courses );
    }

    /**
     * Gets the last content ID or URL where the student left off
     * 
     * @param int $course_id The course ID
     * @param bool $url either we want the url or just the ID
     * 
     * @return string|int the content ID or the URL to it
     */
    public function get_last_content( $course_id, $url = false ) {
        $last_content_id = $this->get( 'coschool_last_seen_' . $course_id );

        if( '' == $last_content_id ) return false;

        if( $url ) {
            return get_permalink( $last_content_id );
        }

        return $last_content_id;
    }

    /**
     * Total amount spent by a student
     * 
     * @since 0.9
     * 
     * @return float|int
     */
    public function get_spent() {
        global $wpdb;

        $spent = $wpdb->get_row( $wpdb->prepare( "SELECT SUM(`amount`) AS `amount` FROM `{$wpdb->prefix}{$this->db_prefix}payments` WHERE `student` = %d", $this->get( 'id' ) ) );

        return is_null( $spent->amount ) ? 0 : $spent->amount;
    }

    public function mark_complete( $content_id, $reference = '' ) {
        $course_id      = get_post_meta( $content_id, 'course_id', true ); // @TODO use appropriate method
        $enrollment_id  = $this->get_course_enrollment( $course_id )->id;
        $is_completed   = $this->has_completed( $content_id );
        $mark_complete  = $is_completed ? $is_completed : $this->database->add_enrollment_progress( $enrollment_id, $content_id, $reference );
        return $mark_complete;
    }

    /**
     * Get progress of a course
     * 
     * @return percent value. Eg. 0.8 means 80%, 0.1 means 10%
     */
    public function get_progress( $course_id, $percent = true ) {
        if( ! $this->has_course( $course_id ) ) {
            return false;
        }

        global $wpdb;
        $course_data = new Course_Data( $course_id );

        $completed_count    = $wpdb->query( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}enrollment_progress` WHERE `enrollment_id` = %d", $this->get_course_enrollment( $course_id )->id ) );
        $content_count      = count( $course_data->get_contents() );

        $progress = $content_count == 0 ? 0 : round( $completed_count / $content_count * ( true === $percent ? 100 : 1 ), 2 );

        return apply_filters( 'coschool_course_progress_percent', $progress, $course_id, $this->get( 'id' ) );
    }

    /**
     * Get overall progress of a student
     * 
     * @return percent value. Eg. 0.8 means 80%, 0.1 means 10%
     */
    public function get_overall_progress( $percent = true ) {

        // return '65%'; //@TODO use appropriate method
        $total_courses =  $this->get_courses();
        $total_progress = '0';
        $total_courses_complete = [];
        foreach ( $total_courses as $course ) {
            // Helper::pri( $course );
           
            global $wpdb;
            
            $course_data = new Course_Data( $course );
        
            $completed_count    = $wpdb->query( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}enrollment_progress` WHERE `enrollment_id` = %d", $this->get_course_enrollment( $course )->id ) );

            $content_count      = count( $course_data->get_contents() );

            $progress = $content_count == 0 ? 0 : round( $completed_count / $content_count * ( true === $percent ? 100 : 1 ), 2 );
            $total_progress +=  $progress ;
            $total_courses_complete[] = $completed_count;

        }
            $count =  count( $total_courses_complete );
            if( $count == 0 ){
                return $overall_progress = 0 .'%';
            }
            $overall_progress = ( $total_progress / $count );
            return round ( $overall_progress ) . '%';
    }

    /**
     * If the current student has completed the given item
     * 
     * @param int $content_id The ID of the lesson, quiz or assignment
     * 
     * @since 0.9
     * 
     * @return bool
     */
    public function has_completed( $content_id ) {
        global $wpdb;
        
        if( get_post_type( $content_id ) == 'course' ) {
            return $wpdb->query( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}enrollments` WHERE `course_id` = %d AND `student` = %d AND `status` = %s", $content_id, $this->get( 'id' ), 'completed' ) ) > 0;
        }

        return $wpdb->query( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}enrollment_progress` WHERE `content_id` = %d AND `enrollment_id` IN (SELECT `id` FROM `{$wpdb->prefix}{$this->db_prefix}enrollments` WHERE `student` = %d)", $content_id, $this->get( 'id' ) ) ) > 0;
    }

    /**
     * Gets wishlist items of this student
     * 
     * @return [] an array of course IDs
     */
    public function get_wishlist() {
        $wishlist = $this->get( 'wishlist_items' );

        if( in_array( $wishlist, [ null, '' ] ) ) {
            return [];
        }

        return unserialize( $wishlist );
    }

    /**
     * Add new course to the wishlist
     * 
     * @param int $course_id
     */
    public function add_to_wishlist( $course_id ) {
        $wishlist   = $this->get_wishlist();
        
        $wishlist[] = $course_id;
        
        $this->set( 'wishlist_items', serialize( array_unique( $wishlist ) ) );
    }

    /**
     * Remove a course from the wishlist
     * 
     * @param int $course_id
     */
    public function remove_from_wishlist( $course_id ) {
        $wishlist = $this->get_wishlist();

        if ( in_array( $course_id , $wishlist ) ) {
            unset( $wishlist[ array_search( $course_id, $wishlist ) ] );
        }

        $this->set( 'wishlist_items', serialize( $wishlist ) );
    }

    /**
     * return prerequisites list
     * 
     * @param int $content_id
     */
    public function has_prerequisites( $content_id ) {
        $content_type   = get_post_type( $content_id );

        $prerequisites  = [];
        if ( ! in_array( $content_type, [ 'lesson', 'quiz', 'assignment' ] ) ) return $prerequisites;

        $data_class     = "\Codexpert\CoSchool\App\\". ucfirst( $content_type ) ."\Data";
        $content_data   = new $data_class( $content_id );

        $prerequisites  = $content_data->get_prerequisites();

        if( ! $prerequisites ) return $prerequisites;

        $_prerequisites = $prerequisites;
        $prerequisites  = [];

        foreach ( $_prerequisites as $prerequisite ) {
            if ( ! $this->has_completed( $prerequisite ) ) $prerequisites[] = $prerequisite;
        }

        return $prerequisites;
    }

    public function get_payments() {
        global $wpdb;

        $payments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}payments` WHERE `student` = %d", $this->get( 'id' ) ) );

        return $payments;
    }
}