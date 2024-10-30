<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Lesson;
use Codexpert\CoSchool\Abstracts\Post_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\Helper;

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
class Data extends Post_Data {

    /**
     * @var obj
     */
    public $lesson;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $lesson the lesson
     */
    public function __construct( $lesson ) {
        $this->lesson = get_post( $lesson );
        parent::__construct( $this->lesson );
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
     * Lesson type
     * 
     * @return free|premium
     */
    public function get_status() {
        return get_post_status( $this->get( 'id' ) );
    }

    /**
     * Gets the course thumbnail id
     * 
     * @return int The ID
     */
    public function get_thumbnail_id() {
        return get_post_meta( $this->get( 'id' ), '_thumbnail_id', true );
    }

    /**
     * Gets the course thumbnail url
     * 
     * @return string The URL
     */
    public function get_thumbnail_url( $size = 'coschool-thumb' ) {
        $default_img = plugins_url( 'assets/img/no-thumbnail.jpg', COSCHOOL );
        return get_the_post_thumbnail_url( $this->get( 'id' ), $size ) ? : $default_img;
    }

    /**
     * Gets the banner
     * 
     * @return string The URL
     */
    public function get_banner( $size = 'coschool-banner' ) {
        return get_the_post_thumbnail( $this->get( 'id' ), $size );
    }

    public function get_banner_id() {
        return get_post_meta( $this->get( 'id' ), 'lesson_banner', true );
    }

    /**
     * Gets the course banner url
     * 
     * @return string The URL
     */
    public function get_banner_url( $size = 'coschool-banner' ) {
        $default_img    = plugins_url( 'assets/img/no-thumbnail.jpg', COSCHOOL );
        $banner_id      = get_post_meta( $this->get( 'id' ), 'lesson_banner', true );

        return wp_get_attachment_image_url( $banner_id, $size ) ? : $default_img;
    }

    public function is_free() {
       $config = $this->get( 'coschool_lesson_config' );

       return isset( $config['free_lesson'] );
    }

    public function is_allow_comment() {
       return $this->get( 'allow_comment' );
    }

    public function get_type() {
       $config = $this->get( 'coschool_lesson_config' );

       return $config;
    }

    /**
     * Can a student see this content?
     * 
     * @param int $user_id the student ID
     * 
     * @return bool
     */
    public function is_visible_by( $user_id = null ) {

        if( $this->is_free() ) {
            return true;
        }

        if( is_null( $user_id ) ) {
            $user_id = get_current_user_id();
        }

        if( user_can( $user_id, 'edit_post', $this->get( 'id' ) ) ) {
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

    public function get_discussion() {
        
        $args = [
            'post_id' => $this->get( 'id' )
        ];

        return get_comments( $args );
    }

    public function get_author_name() {
        return get_the_author_meta( 'display_name', get_the_author_meta('ID') );
    }

    public function get_author_avatar() {
        return get_avatar( get_the_author_meta('ID') );
    }

    /**
     * If a user has access to this lesson
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

        return $course_data->has_access( $user_id ) || $this->is_free();
    }
}