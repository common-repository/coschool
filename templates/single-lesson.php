<?php
namespace Codexpert\CoSchool\App;
/**
 * The Template for displaying all single lessons
 *
 * This template can be overridden by copying it to yourtheme/coschool/single-lesson.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $lesson_data, $course_data, $instructor_data, $student_data;

$lesson_data		= new Lesson\Data( get_the_ID() );
$course_data		= new Course\Data( $lesson_data->get( 'course_id' ) );
$instructor_data	= new Instructor\Data( $course_data->get( 'author' ) );
$student_data 		= new Student\Data( get_current_user_id() );

get_header( 'lessons' );

	/**
	 * coschool_before_main_content hook.
	 */
	do_action( 'coschool_before_main_content' );
	
	while ( have_posts() ) :
		the_post();

		/**
		 * Hook: coschool_single_lesson.
		 */
		do_action( 'coschool_single_lesson' );
	endwhile;

	/**
	 * coschool_after_main_content hook.
	 */
	do_action( 'coschool_after_main_content' );

	/**
	 * coschool_sidebar hook.
	 */
	do_action( 'coschool_sidebar' );

get_footer( 'lessons' );