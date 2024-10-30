<?php
/**
 * All quiz related functions
 */
namespace Codexpert\CoSchool\App\Quiz;
use Codexpert\CoSchool\Helper;
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
 * @subpackage Quiz
 * @author Codexpert <hi@codexpert.io>
 */
class Data extends Post_Data {

    /**
     * @var obj
     */
    public $quiz;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $quiz the quiz
     */
    public function __construct( $quiz ) {
        $this->quiz = get_post( $quiz );
        parent::__construct( $this->quiz );
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

    public function get_banner_id() {
        return get_post_meta( $this->get( 'id' ), 'quiz_banner', true );
    }

    /**
     * Gets the course banner url
     * 
     * @return string The URL
     */
    public function get_banner_url( $size = 'coschool-banner' ) {
        $default_img    = plugins_url( 'assets/img/no-thumbnail.jpg', COSCHOOL );
        $banner_id      = get_post_meta( $this->get( 'id' ), 'quiz_banner', true );

        return wp_get_attachment_image_url( $banner_id, $size ) ? : $default_img;
    }

    public function is_enable_deadline() {
       $config = $this->get( 'coschool_quiz_config_deadline' );
       return isset( $config['enable_dead_line'] );
    }

    public function config() {
       return $this->get( 'coschool_quiz_config' );
    }

    public function deadline() {
       return $this->get( 'coschool_quiz_config_deadline' );
    }

    public function retake() {
       return $this->get( 'coschool_quiz_config_retake' );
    }

    public function time() {
       return $this->get( 'coschool_quiz_config_time' );
    }

    public function prerequisites() {
       return $this->get( 'coschool_quiz_prerequisites' );
    }

    /**
     * Gets all questions of this quiz
     * 
     * @return [int|obj] the questions $post_id|$post
     */
    public function list_questions() {
        return $this->get( 'questions' );
    }

    /**
     * Gets the answer of a question
     * 
     * @return int|obj the answer $post_id|$post
     */
    public function get_answer( $question_id ) {
        return [];
    }

    /**
     * Lists attempts to this quiz by student(s)
     * 
     * @param int $student_id
     * 
     * @return [attemps]
     */
    public function get_attempts( $student_id = null ) {
        $attemps = new Attempt( $this->get( 'id' ) );
        return $attemps->list( $student_id );
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

    /**
     * If a user has access to this quiz
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
     * Return config data
     * 
     * @param string $section the config section
     * @param string $key the array key
     * 
     * @return bool | String | Array 
     */
    public function get_config( $section = 'coschool_quiz_config', $key = 'all' ) {

        $config = $this->get( $section );

        if( $key == 'all' ) return $config;

        if( isset( $config[ $key ] ) ) $config[ $key ];

        return false;
    }

    /**
     * If a student get pass mark
     * 
     * @param int $student_id 
     * 
     * @return bool
     * 
     * @since 0.9
     * 
     * @author Jakaria Istauk <jakariamd35@gmail.com>
     * 
     * @todo review this function
     */
    public function is_passed( $student_id ) {

        if( ! $student_id ) return false;

        $config = $this->get_config();
        if ( ! isset( $config['pass_mark'] ) || $config['pass_mark'] == '' ) return true;

        $attempts       = new Attempt( $this->get( 'id' ) );
        $attempt_list   = $attempts->list( $student_id );
        $pass_mark      = $config['pass_mark'];

        if ( $attempt_list ) {
            foreach ( $attempt_list as $attempt ) {
                $answers = $attempts->get_an_attempt_answers( $attempt->id );
                $_pass_mark = 0;
                foreach ( $answers as $key => $answer ) {
                    $_pass_mark += $answer->points;
                }
                if( $_pass_mark >= $pass_mark ) {
                    return true;
                } 
            }
        }

        return false;
    }

    /**
     * Leaderboard of a quiz
     */
    public function get_leaderboard() {
        $attempt_data = new Attempt( $this->get( 'id' ) );
        $attempts = $attempt_data->list();

        $leaderboard = [];
        foreach ( $attempts as $attempt ) {
            foreach ( $attempt_data->get_an_attempt_answers( $attempt->id ) as $answer ) {

                if( ! isset( $leaderboard[ $attempt->student ] ) ) {
                    $leaderboard[ $attempt->student ] = 0;
                }

                $leaderboard[ $attempt->student ] += $answer->points;
            }
        }

        return $leaderboard;
    }

    /**
     * Gets score of a student if they participated ina quiz
     * 
     * @return int
     */
    public function get_score( $student_id ) {
        $leaderboard = $this->get_leaderboard();

        return isset( $leaderboard[ $student_id ] ) ? $leaderboard[ $student_id ] : 0;
    }

    /**
     * Rank of a student for a quiz
     */
    public function get_rank( $student_id ) {
        $index = array_search( $student_id, array_keys( $this->get_leaderboard() ) );

        return $index + 1; // we need the actual POSITION in the list, not the array index
    }
    public function quiz_attempt_status( $student_id ) {

        if( ! $student_id ) return false;

        $attempts       = new Attempt( $student_id );
        $attempt_list   = $attempts->list( $student_id );

        if ( $attempt_list ) {
            foreach ( $attempt_list as $attempt ) {}
        }

    }
}