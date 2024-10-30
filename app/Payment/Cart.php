<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Payment;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\Instructor\Data as Instructor_Data;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\Course_Bundle\Data as Bundle_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Payment
 * @author Codexpert <hi@codexpert.io>
 */
class Cart {

    /**
     * @var obj $wpdb
     */
    public $database;

    public $cart_key = 'coschool_cart';

    public $coupon_key = 'coschool_coupon';

    /**
     * Constructor function
     * 
     * @uses WP_Post class
     * @param int|obj $method the method
     */
    public function __construct() {
        $this->database = new DB;
    }

    /**
     * Gets the cart
     * 
     * @return [int] An array of IDs
     */
    public function get_contents() {
        if( isset( $_COOKIE[ $this->cart_key ] ) ) {
            return unserialize( stripslashes( coschool_sanitize( $_COOKIE[ $this->cart_key ] ) ) );
        }

        return false;
    }

    public function get_courses() {
        $contents = $this->get_contents();
        $courses = [];
        if( ! is_array( $contents ) || count( $contents ) <= 0 ) return [];
        foreach ( $contents as $content_id ) {
            if( get_post_type( $content_id ) == 'course' ) {
                $courses[] = $content_id;
            }

            elseif( get_post_type( $content_id ) == 'bundle' ) {
                $bundle_data = new Bundle_Data( $content_id );
                foreach ( $bundle_data->get_courses() as $bundle_course_id ) {
                	$courses[] = $bundle_course_id;
                }
            }
        }
        
        return $courses;
    }

    /**
     * Gets total orginal price 
     * 
     * @return [int]
     */
    public function get_courses_price() {
        $total = 0;
        foreach ( $this->get_courses() as $course_id ) {
            $course_data = new Course_Data( $course_id );
            $total += $course_data->get_price();
        }
        return $total;
    }

    /**
     * Gets the cart
     * 
     * @return [int] An array of IDs
     */
    public function get_coupons() {
        return isset( $_COOKIE[ $this->coupon_key ] ) ? coschool_sanitize( $_COOKIE[ $this->coupon_key ] ) : false;
    }

    /**
     * Adds a course to the cart
     * 
     * @param int $course_id
     */
    public function add_course( $course_id ) {

        if( false === ( $cart = $this->get_contents() ) ) {
            $cart = [];
        }

        $cart[] = $course_id;

        setcookie(  $this->cart_key , serialize( array_unique( $cart ) ), time() + WEEK_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
    }

    /**
     * Adds a course to the cart
     * 
     * @param int $course_id
     */
    public function remove_course( $course_id ) {

        if( false === ( $cart = $this->get_contents() ) ) {
            return;
        }

        if( in_array( $course_id, $cart ) && ( $key = array_search( $course_id, $cart ) ) !== false ) {
            unset( $cart[ $key ] );
        }

        setcookie( $this->cart_key , serialize( array_unique( $cart ) ), time() + WEEK_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
    }

    /**
     * Clears the cart
     */
    public function clear_cart() {
        setcookie( $this->cart_key , '', time() -1, COOKIEPATH, COOKIE_DOMAIN );
    }

    /**
     * Removes coupons
     */
    public function remove_coupons() {
        setcookie( $this->coupon_key , '', time() -1, COOKIEPATH, COOKIE_DOMAIN );
    }

    /**
     * General loader for some early actions
     */
    public function loader() {
        
        // a course is being added to the cart
        if( isset( $_GET['enroll'] ) && get_post_status( $course_id = coschool_sanitize( $_GET['enroll'] ) ) ) {

            $this->add_course( $course_id );

            do_action( 'coschool-course_added_to_cart', $course_id );
            
            wp_safe_redirect( coschool_enroll_page( true ) );
            exit();
        }
        
        // removing a course from the cart
        if( isset( $_GET['delist'] ) && get_post_status( $course_id = coschool_sanitize( $_GET['delist'] ) ) ) {

            $this->remove_course( $course_id );

            do_action( 'coschool-course_removed_from_cart', $course_id );
            
            wp_safe_redirect( coschool_enroll_page( true ) );
            exit();
        }
        
        // removing a coupon from the cart
        if( isset( $_GET['coupon-remove'] ) && 'true' == coschool_sanitize( $_GET['coupon-remove'] ) ) {
            
            $this->remove_coupons();
            
            wp_safe_redirect( coschool_enroll_page( true ) );
            exit();
        }

        // enrollment form is submitted
        if( isset( $_POST['coschool-enrollment'] ) ) {
            do_action( 'coschool-enroll_form_submitted', $_POST );
        }
        
    }

    /**
     * Let's process the payment
     * 
     * @param array $posted The form data
     */
    public function process_enrollment( $posted ) {

        // create or log the user in
        $user_id = get_current_user_id();

        // @TODO validate and verify
        if( $user_id == 0 ) {
            $user_id = wp_insert_user( [
                'user_login'    => $posted['email'],
                'user_email'    => $posted['email'],
                'first_name'    => $posted['first_name'],
                'last_name'     => $posted['last_name'],
                'user_pass'     => $posted['password'],
                'role'          => 'student',
            ] );

            // log the student in
            coschool_auto_login( $posted['email'] );
        }

        // add required cap to the student
        $student = new \WP_User( $user_id );
        if( ! $student->has_cap( 'read_courses' ) ) {
            $student->add_cap( 'read_courses' );
            $student->add_cap( 'upload_files' );
            // $student->add_cap('edit_attachments');
        }

        /**
         * Insert payment data
         * 
         * @todo include `method` name with the hook below and add action to appropriate hook only
         */
        $payment_id = apply_filters( "coschool_{$posted['payment-method']}_payment_id", 0, $posted );

        /**
         * Insert the enrollment data
         * 
         */
        $total_paid             = coschool_get_cart_totals( 'total' );
        $total_courses_price    = $this->get_courses_price();

        foreach ( $this->get_courses() as $course_id ) {
            $course_data            = new Course_Data( $course_id );
            $course_orginal_price   = $course_data->get( 'price' );
            $total                  = $course_orginal_price * $total_paid;
            if ( $total ) {
                $total              = ( $course_orginal_price * $total_paid ) /  $total_courses_price;
            }

            $status                 = $course_data->get( 'price' ) == 0 || $payment_id != 0 ? 'active' : 'pending';
            //test payment 
            if ( $payment_id == -1 ) {
               $total = 0;
            }
            $enrollment_id          = $this->database->insert_enrollment( $course_id, $user_id, $total, $payment_id, $status );
            
            /**
             * Insert enrollment meta
             */
            $instructor = new Instructor_Data( $course_data->get_instructor() );
            $this->database->add_enrollment_meta( $enrollment_id, 'instructor_id', $instructor->get( 'id' ) );
            $this->database->add_enrollment_meta( $enrollment_id, 'instructor_commission_rate', $instructor->get_commission() );
            $this->database->add_enrollment_meta( $enrollment_id, 'instructor_earning', ( $total * $instructor->get_commission() / 100 ) );
        }

        do_action( 'coschool_course_enroll', $user_id, $this->get_contents() );

        /**
         * Clear the cart and remove coupons
         */
        $this->clear_cart();
        $this->remove_coupons();
        
        /**
         * Rediret to somewhere
         */
        wp_safe_redirect( coschool_dashboard_page( true ) );
        exit();
    }

    public function remove_wishlist_items( $user_id, $courses ) {
        $student_data = new Student_Data( $user_id );
        
        foreach ( $courses as $course_id ) {
            $student_data->remove_from_wishlist( $course_id );
        }
    }
}