<?php
/**
 * All question related functions
 */
namespace Codexpert\CoSchool\App\Question;
use Codexpert\CoSchool\Abstracts\Post_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Question
 * @author Codexpert <hi@codexpert.io>
 */
class Data extends Post_Data {

    /**
     * @var obj
     */
    public $question;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $question the question
     */
    public function __construct( $question ) {
        $this->question = get_post( $question );
        parent::__construct( $this->question );
    }

    /**
     * Gets question type
     * 
     * @return string 
     */
    public function get_type() {
        return $this->get( 'question_type' );
    }

    /**
     * Gets the correct answer of a question
     * 
     * @return mix
     */
    public function get_answer() {
        $_answer = $this->get( 'question_correct' );

        return coschool_unserialize( $_answer );
    }

    /**
     * Gets the point of a answer
     * 
     * @return int|float
     */
    public function get_point() {
        $point = $this->get( 'question_mark' );

        return $point;
    }
}