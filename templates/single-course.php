<?php
namespace Codexpert\CoSchool\App;
/**
 * The Template for displaying all single courses
 *
 * This template can be overridden by copying it to yourtheme/coschool/single-course.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $course_data, $instructor_data, $student_data;

$course_data		= new Course\Data( get_the_ID() );
$instructor_data	= new Instructor\Data( $course_data->get( 'author' ) );
$student_data 		= new Student\Data( get_current_user_id() );

get_header( 'courses' );

	/**
	 * coschool_before_main_content hook.
	 */
	do_action( 'coschool_before_main_content' );
	
	while ( have_posts() ) :
		the_post();

		/**
		 * Hook: coschool_single_course.
		 */
		do_action( 'coschool_single_course' );
	endwhile;

	/**
	 * coschool_after_main_content hook.
	 */
	do_action( 'coschool_after_main_content' );

	/**
	 * coschool_sidebar hook.
	 */
	do_action( 'coschool_sidebar' );

get_footer( 'courses' );