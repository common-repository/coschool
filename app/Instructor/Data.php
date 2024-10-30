<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Instructor;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\Abstracts\User_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;

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
class Data extends User_Data {

    /**
     * @var obj
     */
    public $instructor;

    public $db_prefix;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $instructor the instructor
     */
    public function __construct( $instructor ) {
        $this->instructor = get_userdata( $instructor );
        parent::__construct( $this->instructor );

        $this->db_prefix = coschool_db_prefix();
    }

    public function get_courses() {
        return Helper::get_posts( [ 'post_type' => 'course', 'author' => $this->instructor->ID ] );
    }

    public function get_lessons() {
        return Helper::get_posts( [ 'post_type' => 'lesson', 'author' => $this->instructor->ID ] );
    }

    public function get_quizzes() {
        return Helper::get_posts( [ 'post_type' => 'quiz', 'author' => $this->instructor->ID ] );
    }

    public function get_assignments() {
        return Helper::get_posts( [ 'post_type' => 'assignment', 'author' => $this->instructor->ID ] );
    }

    public function get_archive_url() {
        return get_author_posts_url( $this->get( 'id' ) );
    }

    public function get_students() {
        $courses    = $this->get_courses();
        $_students  = [];

        foreach ( $courses as $course_id => $course_name ) {
            $course_data    = new Course_Data( $course_id );
            $_students      = array_merge( $course_data->get_students(), $_students );
        }

        $students = [];
        foreach ( $_students as $student ) {
            $students[] = $student->student;
        }

        return $students;
    }

    public function get_earnings() {
        
        global $wpdb;

        $earnings = $wpdb->get_row( $wpdb->prepare( "SELECT SUM(`meta_value`) AS `amount` FROM `{$wpdb->prefix}{$this->db_prefix}enrollmentmeta` WHERE `meta_key` = 'instructor_earning' AND `enrollment_id` IN (SELECT `enrollment_id` FROM `{$wpdb->prefix}{$this->db_prefix}enrollmentmeta` WHERE `meta_key` = 'instructor_id' AND `meta_value` = %d)", $this->get( 'id' ) ) );

        return is_null( $earnings->amount ) ? 0 : $earnings->amount;
    }

    /**
     * Avegare rating from this instructor's all courses
     * 
     * @return int|float
     */
    public function get_rating() {
        $rating = $course_count = 0;

        foreach ( $this->get_courses() as $course_id => $name ) {
            $course_data = new Course_Data( $course_id );
            if( ( $course_rating = $course_data->get_rating() ) > 0 ) {
                $rating += $course_rating;
                $course_count++;
            }
        }

        if( 0 == $course_count ) return 0;

        return round( $rating / $course_count, 2 );
    }

    public function get_commission() {

        $commission_rate = $this->get( 'commission_rate' );

        if( '' == $commission_rate ) {

            if( '' == Helper::get_option( 'coschool_multi_instructor', 'commission_rate' ) ) {
                 return 100;
            }
            else{
                return Helper::get_option( 'coschool_multi_instructor', 'commission_rate' );
            }
        }

        return $commission_rate;
    }

    public function get_enrollments( $payment_id = '' ) {
        global $wpdb;

        if ( empty( $payment_id ) ) {
            $_enrollments = [];
            foreach ( $this->get_courses() as $course_id => $name ) {
                $_enrollments[] = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}enrollments` WHERE `course_id` = %d", $course_id ) );
            }

            $enrollments = [];
            foreach ( $_enrollments as $_enrollment ) {
                foreach ( $_enrollment as $enrollment ) {
                    $enrollments[] = $enrollment;
                }
            }
        }
        else {
            $enrollments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}enrollments` WHERE `payment_id` = %d", $payment_id ) );
        }

        return $enrollments;
    }

    public function get_payments() {
        global $wpdb;

        $payments = [];
        foreach ( $this->get_enrollments() as $enrollment ) {
            $payment_id = $enrollment->payment_id;

            if( $payment_id != 0 ) {
            $payments[ $payment_id ] = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}{$this->db_prefix}payments` WHERE `id` = %d", $payment_id ) );
            }
        }

        return $payments;
    }

    /**
     * User's avatar URL
     * 
     * @return string
     */
    public function get_signature_url() {
        return $this->get( '_coschool_sign' );
    }
}