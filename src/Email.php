<?php
/**
 * Email handler
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\Lesson\Data as Lesson_Data;
use Codexpert\CoSchool\App\Quiz\Data as Quiz_Data;
use Codexpert\CoSchool\App\Assignment\Data as Assignment_Data;
use Codexpert\CoSchool\App\Instructor\Data as Instructor_Data;
use Codexpert\CoSchool\App\Student\Data as Student_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Email
 * @author Codexpert <hi@codexpert.io>
 */
class Email extends Base {

	public $plugin;

	private $settings;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->settings = get_option( 'coschool_email' );
	}

	public function init() {
		
		$this->filter( 'wp_mail_content_type', 'set_content_type' );
		$this->action( 'coschool_assignment_submitted', 'assignment_submitted', 10, 2 );
		$this->action( 'coschool_application_submitted', 'application_submitted', 10, 2 );
		$this->action( 'coschool_application_approval', 'application_approval', 10, 2 );
		$this->action( 'coschool_quiz_submitted', 'quiz_submitted', 10, 2 );
		$this->action( 'coschool_quiz_passed', 'quiz_passed', 10, 2 );
		$this->action( 'coschool_course_enroll', 'course_enroll', 10, 2 );
		
	}

	public function set_content_type( $type ) {
		return "text/html";
	}

	/**
	 * Replaces placeholders with the actual data
	 * 
	 * @param string $string
	 * @param int $student_id
	 * @param int $instructor_id
	 * @param int $content_id
	 * 
	 * @since 0.9
	 * 
	 * @return string
	 * 
	 * @todo improve
	 */
	public function replace_placeholders( $string, $args = [] ) {

		$institution_name		= coschool_site_name();
		$institution_url		= coschool_site_url();

		$course_id				= '';
		$course_name			= '';
		$course_url				= '';

		$lesson_id				= '';
		$lesson_name			= '';
		$lesson_url				= '';

		$assignment_id			= '';
		$assignment_name		= '';
		$assignment_url			= '';

		$quiz_id				= '';
		$quiz_name				= '';
		$quiz_url				= '';
		$quiz_review_url		= '';

		$instructor_id			= '';
		$instructor_name		= '';
		$instructor_url			= '';
		$instructor_status		= '';
		
		$student_id				= '';
		$student_name			= '';
		
		$leaderboard_position	= '';

		// we have the lesson ID
		if( isset( $args['lesson_id'] ) ) {
			$lesson_data 	= new Lesson_Data( $args['lesson_id'] );

			$lesson_id		= $lesson_data->get( 'id' );
			$lesson_name	= $lesson_data->get( 'name' );
			$lesson_url		= $lesson_data->get( 'url' );

			$args['course_id'] = $lesson_data->get_course();
		}

		// we have the quiz ID
		if( isset( $args['quiz_id'] ) ) {
			$quiz_data = new Quiz_Data( $args['quiz_id'] );

			$quiz_id			= $quiz_data->get( 'id' );
			$quiz_name			= $quiz_data->get( 'name' );
			$quiz_url			= $quiz_data->get( 'url' );
			$quiz_review_url	= add_query_arg( [ 'page' => 'quiz-attempts', 'quiz' => $args['quiz_id'] ], admin_url( 'admin.php' ) );

			$args['course_id'] = $quiz_data->get_course();
		}

		// we have the assignment ID
		if( isset( $args['assignment_id'] ) ) {
			$assignment_data 	= new Assignment_Data( $args['assignment_id'] );

			$assignment_id		= $assignment_data->get( 'id' );
			$assignment_name	= $assignment_data->get( 'name' );
			$assignment_url		= $assignment_data->get( 'url' );

			$args['course_id'] 	= $assignment_data->get_course();
		}

		// we have the course ID
		if( isset( $args['course_id'] ) ) {
			$course_data	= new Course_Data( $args['course_id'] );

			$course_id		= $course_data->get( 'id' );
			$course_name	= $course_data->get( 'name' );
			$course_url		= $course_data->get( 'url' );

			$args['instructor_id'] = $course_data->get_instructor();
		}

		// we have the instructor ID
		if( isset( $args['instructor_id'] ) ) {
			$instructor_data	= new Instructor_Data( $args['instructor_id'] );

			$instructor_id		= $instructor_data->get( 'id' );
			$instructor_name	= $instructor_data->get( 'name' );
			$instructor_url		= $instructor_data->get( 'url' );
			$instructor_status	= $instructor_data->get( 'coschool_user_status' );
		}

		// we have the student ID
		if( isset( $args['student_id'] ) ) {
			$student_data	= new Student_Data( $args['student_id'] );

			$student_id		= $student_data->get( 'id' );
			$student_name	= $student_data->get( 'name' );
		}

		// if we have both the quiz and student ID
		if( isset( $args['quiz_id'] ) && isset( $args['student_id'] ) ) {
			$leaderboard_position = $quiz_data->get_rank( $student_id );
		}

		$placeholders = apply_filters( 'coschool_email_placeholders', [
			'%%institution_name%%'		=> $institution_name,
			'%%institution_url%%'		=> $institution_url,
			'%%course_id%%'				=> $course_id,
			'%%course_name%%'			=> $course_name,
			'%%course_url%%'			=> $course_url,
			'%%lesson_id%%'				=> $lesson_id,
			'%%lesson_name%%'			=> $lesson_name,
			'%%lesson_url%%'			=> $lesson_url,
			'%%assignment_id%%'			=> $assignment_id,
			'%%assignment_name%%'		=> $assignment_name,
			'%%assignment_url%%'		=> $assignment_url,
			'%%quiz_id%%'				=> $quiz_id,
			'%%quiz_name%%'				=> $quiz_name,
			'%%quiz_url%%'				=> $quiz_url,
			'%%quiz_review_url%%'		=> $quiz_review_url,
			'%%instructor_id%%'			=> $instructor_id,
			'%%instructor_name%%'		=> $instructor_name,
			'%%instructor_url%%'		=> $instructor_url,
			'%%instructor_status%%'		=> $instructor_status,
			'%%student_id%%'			=> $student_id,
			'%%student_name%%'			=> $student_name,
			'%%leaderboard_position%%'	=> $leaderboard_position,
		] );

		return stripslashes( str_replace( array_keys( $placeholders ), array_values( $placeholders ), $string ) );
	}

	/**
	 * Send the email
	 */
	public function send( $to, $subject, $message = '', $headers = '', $attachments = array() ) {
		$content = '';

		// include the header template
		$content .= Helper::get_view( 'email-header', 'templates', [ 'to' => $to, 'subject' => $subject ] );

		// the actual message
		$content .= wpautop( $message );
		
		// include the footer template
		$content .= Helper::get_view( 'email-footer', 'templates', [ 'to' => $to, 'subject' => $subject ] );

		wp_mail( $to, $subject, $content, $headers, $attachments );
	}

	/**
	 * Send notification email to the instructor after assignment submitted
	 * 
	 * @param int $assignment_id
	 * @param int $student_id
	 */
	public function assignment_submitted( $assignment_id, $student_id ) {

		if ( Helper::get_option( 'coschool_email','assignment_submission_enabled' ) == '' ) return;

		$assignment_data 	= new Assignment_Data( $assignment_id );
		$course_id 			= $assignment_data->get_course();
        $course_data    	= new Course_Data( $course_id );
        $instructor_id 		= $course_data->get_instructor();

		$instructor_data 	= new Instructor_Data( $instructor_id );

		$args = [
			'student_id'	=> $student_id,
			'assignment_id'	=> $assignment_id,
		];

		$subject	= $this->replace_placeholders( $this->settings['assignment_instructor_subject'], $args );
		$body		= $this->replace_placeholders( $this->settings['assignment_instructor_body'], $args );
		$this->send( $instructor_data->get( 'email' ), $subject, $body );
	}

	/**
	 * Send notification email to the instructor and admin after application submitted
	 * 
	 * @param int $instructor_id
	 * @param string $status
	 */
	public function application_submitted( $instructor_id, $status ) {

		if ( Helper::get_option( 'coschool_email','application_submission_enabled' ) == '' ) return;

		$instructor_data = new Instructor_Data( $instructor_id );

		$args = [
			'instructor_id'	=> $instructor_id,
		];

		// send to instructor
		$subject	= $this->replace_placeholders( $this->settings['application_submitted_instructor_subject'], $args );
		$body		= $this->replace_placeholders( $this->settings['application_submitted_instructor_body'], $args );
		$this->send( $instructor_data->get( 'email' ), $subject, $body );

		// send to admin
		$subject	= $this->replace_placeholders( $this->settings['application_submitted_admin_subject'], $args );
		$body		= $this->replace_placeholders( $this->settings['application_submitted_admin_body'], $args );
		$this->send( coschool_admin_email(), $subject, $body );
	}

	/**
	 * Send notification email to the instructor after application approve
	 * 
	 * @param int $instructor_id
	 * @param string $status
	 */
	public function application_approval( $instructor_id, $status ) {

		if ( Helper::get_option( 'coschool_email','application_approval_enabled' ) == '' ) return;

		$instructor_data = new Instructor_Data( $instructor_id );

		$args = [
			'instructor_id'	=> $instructor_id,
		];

		$subject	= $this->replace_placeholders( $this->settings['application_approval_instructor_subject'], $args );
		$body		= $this->replace_placeholders( $this->settings['application_approval_instructor_body'], $args );
		$this->send( $instructor_data->get( 'email' ), $subject, $body );
	}

	/**
	 * Send notification email to the instructor after quiz submitted
	 * 
	 * @param int $quiz_id
	 * @param int $student_id
	 */
	public function quiz_submitted( $quiz_id, $student_id ) {

		if ( Helper::get_option( 'coschool_email','quiz_submitted_enabled' ) == '' ) return;

		$quiz_data 			= new Quiz_Data( $quiz_id );
		$course_id 			= $quiz_data->get_course();
        $course_data    	= new Course_Data( $course_id );
        $instructor_id 		= $course_data->get_instructor();
		$instructor_data 	= new Instructor_Data( $instructor_id );

		$args = [
			'student_id'	=> $student_id,
			'quiz_id'		=> $quiz_id,
		];

		$subject	= $this->replace_placeholders( $this->settings['quiz_submitted_instructor_subject'], $args );
		$body		= $this->replace_placeholders( $this->settings['quiz_submitted_instructor_body'], $args );

		$this->send( $instructor_data->get( 'email' ), $subject, $body );
	}

	/**
	 * Send notification email to the student after quiz passed
	 * 
	 * @param int $quiz_id
	 * @param int $student_id
	 */
	public function quiz_passed( $quiz_id, $student_id ) {

		if ( Helper::get_option( 'coschool_email','quiz_passed_enabled' ) == '' ) return;

		$student_Data = new Student_Data( $student_id );

		$args = [
			'student_id'	=> $student_id,
			'quiz_id'		=> $quiz_id,
		];

		$subject	= $this->replace_placeholders( $this->settings['quiz_passed_student_subject'], $args );
		$body		= $this->replace_placeholders( $this->settings['quiz_passed_student_body'], $args );
		$this->send( $student_Data->get( 'email' ), $subject, $body );
	}

	/**
	 * Send notification email to the instructor and student after course enroll
	 * 
	 * @param int $student_id
	 * @param array $courses
	 */
	public function course_enroll( $student_id, $courses ) {

		if ( Helper::get_option( 'coschool_email','enroll_enabled' ) == '' ) return;

		$student_data = new Student_Data( $student_id );

		foreach ( $courses as $course_id ) {
			$course_data    	= new Course_Data( $course_id );
        	$instructor_id 		= $course_data->get_instructor();

			$instructor_data 	= new Instructor_Data( $instructor_id );

			$args = [
				'student_id'	=> $student_id,
				'course_id'		=> $course_id,
			];

			// send to student
			$subject	= $this->replace_placeholders( $this->settings['enroll_course_student_subject'], $sargs );
			$body		= $this->replace_placeholders( $this->settings['enroll_course_student_body'], $args );
			$this->send( $student_data->get( 'email' ), $subject,  wpautop( $body ) );

			// send to instructor
			$subject	= $this->replace_placeholders( $this->settings['enroll_course_instructor_subject'], $args );
			$body		= $this->replace_placeholders( $this->settings['enroll_course_instructor_body'], $args );
			$this->send( $instructor_data->get( 'email' ), $subject, wpautop( $body ) );
		}
	}
}