<?php
namespace Codexpert\CoSchool\App\Assignment;
use Codexpert\CoSchool\App\Course;
use Codexpert\CoSchool\App\Instructor;
use Codexpert\CoSchool\App\Student;
use Codexpert\CoSchool\Helper;
/**
 * The Template for displaying all single assignmentzes
 *
 * This template can be overridden by copying it to yourtheme/coschool/single-assignment.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $assignment_data, $course_data, $instructor_data, $student_data;

$assignment_data	= new Data( get_the_ID() );
$course_data		= new Course\Data( $assignment_data->get( 'course_id' ) );
$instructor_data	= new Instructor\Data( $course_data->get( 'author' ) );
$student_data 		= new Student\Data( get_current_user_id() );

// Helper::pri($instructor_data);

get_header( 'assignments' );

	/**
	 * coschool_before_main_content hook.
	 */
	do_action( 'coschool_before_main_content' );
	
	while ( have_posts() ) :
		the_post();

		/**
		 * Hook: coschool_single_assignment.
		 */
		do_action( 'coschool_single_assignment' );
	endwhile;
	
	/**
	 * coschool_after_main_content hook.
	 */
	do_action( 'coschool_after_main_content' );

	/**
	 * coschool_sidebar hook.
	 */
	do_action( 'coschool_sidebar' );

get_footer( 'assignments' );