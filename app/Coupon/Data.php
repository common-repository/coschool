<?php
/**
 * All coupon related functions
 */
namespace Codexpert\CoSchool\App\Coupon;
use Codexpert\CoSchool\Abstracts\Post_Data;
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
 * @subpackage Coupon
 * @author Codexpert <hi@codexpert.io>
 */
class Data extends Post_Data {

    /**
     * @var obj
     */
    public $coupon;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $coupon the coupon
     */
    public function __construct( $coupon ) {
        $this->coupon = get_post( $coupon );
        parent::__construct( $this->coupon );
    }

    /**
     * Gets associated course IDs
     * 
     * @return [int|obj] An array course ID $post_id|$post
     */
    public function get_courses() {
        return $this->get( 'course_ids' );
    }

    /**
     * Is the coupon enabled?
     * 
     * @return bool
     */
    public function is_enabled() {
        $discount = $this->get( 'coschool_discount' );

        return isset( $discount['enable'] );
    }

    /**
     * Is the coupon valid as of today?
     * 
     * @return bool
     */
    public function has_validity() {
        $condition = $this->get( 'coschool_condition' );

        if( ! isset( $condition['has_validity'] ) ) return true;
        
        if( isset( $condition['valid_from'] ) && '' != $condition['valid_from'] && isset( $condition['valid_to'] ) && '' != $condition['valid_to'] ) {
            $valid_from = strtotime( $condition['valid_from'] );
            $valid_to   = strtotime( $condition['valid_to'] );

            return strtotime( 'today' ) >= $valid_from && strtotime( 'today' ) <= $valid_to;
        }

        return false;
    }

    public function get_type() {
        $discount = $this->get( 'coschool_discount' );
        return isset( $discount['discount_type'] ) && '' != $discount['discount_type'] ? $discount['discount_type'] : 'percent';
    }

    public function get_amount() {
        $discount = $this->get( 'coschool_discount' );
        return isset( $discount['amount'] ) && '' != $discount['amount'] ? $discount['amount'] : 0;
    }

    /**
     * Calculates discounts applied to a course if any
     * 
     * @return int|float
     */
    public function discount_amount( $course_id ) {
        if( ! $this->is_enabled() || ! $this->has_validity() || ! $this->applicable_for( $course_id ) ) return 0;

        // fixed discount
        if( 'fixed' == $this->get_type() ) {
            return $this->get_amount();
        }

        // percent discount
        elseif( 'percent' == $this->get_type() ) {
            $course_data = new Course_Data( $course_id );
            $price = $course_data->get( 'price' );

            return round( $price * $this->get_amount() / 100, 2 );
        }

        return 0;
    }

    /**
     * Is this coupon applicable for the given course?
     * 
     * @param int $course_id
     * 
     * @since 0.9
     * 
     * @return bool
     */
    public function applicable_for( $course_id ) {

        $condition = $this->get( 'coschool_condition' );
        
        // discount applies to 'All'
        if( 'all-courses' == ( $filter_by = $condition['filter_by'] ) ) return true;

        $course_data = new Course_Data( $course_id );

        // discount filtered by course
        if( 'courses' == $filter_by ) {
            return isset( $condition['courses'] ) && in_array( $course_id, $condition['courses'] );
        }

        // discount filtered by instructor
        elseif( 'instructors' == $filter_by ) {
            $instructor_id = $course_data->get( 'instructor' );
            return isset( $condition['instructors'] ) && in_array( $instructor_id, $condition['instructors'] );
        }

        // discount filtered by instructor
        elseif( 'categories' == $filter_by ) {
            $course_categories = array_keys( $course_data->get( 'categories' ) );
            return isset( $condition['categories'] ) && count( array_intersect( $course_categories, $condition['categories'] ) ) > 0;
        }
    }

}