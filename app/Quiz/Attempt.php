<?php
/**
 * All quiz related functions
 * @todo merge this class with Data
 */
namespace Codexpert\CoSchool\App\Quiz;
use Codexpert\CoSchool\Abstracts\DB;
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
class Attempt extends DB {

    /**
     * @var int
     */
    public $quiz_id;

    /**
     * @var int
     */
    public $student;

    /**
     * @var obj
     */
    public $quiz_data;

    /**
     * Constructor function
     */
    public function __construct( $quiz_id ) {
        $this->quiz_id      = $quiz_id;
        $this->quiz_data    = new Data( $this->quiz_id );
        $this->student      = get_current_user_id();

        parent::__construct();
    }

    /**
     * Stores student submitted quiz
     * 
     * @param array $submission An associated array containing user form submission
     *                          Expected format: [ $quesion_id => $answer ]
     * 
     * @since 0.9
     */
    public function store( $submission ) {
        $questions                  = $this->quiz_data->list_questions();
        $time_taken                 = isset( $submission['start_time'] ) &&  $submission['start_time'] != '' ? time() - $submission['start_time'] : 0;
        $config                     = $this->quiz_data->get_config();
        $pass_mark                  = $config['pass_mark'];
        $student_obtained_point     = 0;

        foreach ( $questions as $question_id ) {
            $question_data  = new Question_Data( $question_id );
            $correct_answer = $question_data->get( 'answer' );
            $point          = $question_data->get_point();

            foreach ( $submission['answer'] as $s_question_id => $answer ) {
                if( $question_id == $s_question_id ) {
                    
                    // question point
                    $points = in_array( $question_data->get_type(), [ 'true_false', 'mcq' ] ) && $correct_answer == $answer ? $point : 0;

                    $student_obtained_point += $points;
                }
            }
        }

        if( $student_obtained_point >= $pass_mark ) {

            do_action( 'coschool_quiz_passed', $this->quiz_id  , $this->student );

            $attempt_id = $this->insert_quiz_attempt( $this->quiz_data->get( 'id' ), $this->student, $time_taken, 'passed' );

        }
        else {

            $points_needed      = $pass_mark - $student_obtained_point;
            $point_left         = 0;

            foreach ( $questions as $question_id ) {
                $question_data  = new Question_Data( $question_id );

                if ( $question_data->get_type() == 'text' || $question_data->get_type() == 'paragraph' ) {
                    $point_left += $question_data->get_point();
                }
            }

            if ( $points_needed > $point_left ) {
                $attempt_id = $this->insert_quiz_attempt( $this->quiz_data->get( 'id' ), $this->student, $time_taken,'failed' );
            }
            else {
                $attempt_id = $this->insert_quiz_attempt( $this->quiz_data->get( 'id' ), $this->student, $time_taken,'onhold' );
            }
        }

        foreach ( $questions as $question_id ) {
            $question_data  = new Question_Data( $question_id );
            $correct_answer = $question_data->get( 'answer' );
            $point          = $question_data->get_point();

            foreach ( $submission['answer'] as $s_question_id => $answer ) {
                if( $question_id == $s_question_id ) {
                    
                    // question point
                    $points = in_array( $question_data->get_type(), [ 'true_false', 'mcq' ] ) && $correct_answer == $answer ? $point : 0;

                    // insert attempt answers
                    $this->add_quiz_attempt_answer( $attempt_id, $question_data->get( 'id' ), coschool_serialize( $answer ), $points, '' );
                }
            }
        }

        return $attempt_id;
    }

    public function list( $student = null ) {
        $where = "`quiz_id` = " . $this->quiz_id;

        if( ! is_null( $student ) ) {
            $where .= " AND `student` = {$student}";
        }

        return $this->select( 'quiz_attempts', '*', $where );
    }

    /**
     * Get list of a single attempt
     * 
     * @param int $attempt_id 
     * 
     * @since 0.9
     * 
     * @author Jakaria Istauk <jakariamd35@gmail.com>
     * 
     * @return array 
     * 
     * @todo need to review
     */
    public function get_an_attempt_answers( $attempt_id ) {
        $where = "`attempt_id` = " . $attempt_id;

        return $this->select( 'quiz_attempt_answers', '*', $where );
    }

    /**
     * Get student id of a attempt
     * 
     * @param int $attempt_id 
     * 
     * @return int|bool 
     * 
     * @since 0.9
     * 
     * @author Jakaria Istauk <jakariamd35@gmail.com>
     * 
     * @todo need to review
     */
    public function get_student_id( $attempt_id ) {
        $where = "`id` = " . $attempt_id;

        $attempt = $this->select( 'quiz_attempts', '*', $where );
        return $attempt ? $attempt[0]->student : false ;
    }
    //
    public function get_status( $student_id ) {

        if( ! $student_id ) return false;

        $attempts       = new Attempt( $student_id );
        $attempt_list   = $attempts->list( $student_id );

        if ( $attempt_list ) {
            foreach ( $attempt_list as $attempt ) {}
        }

    }
}