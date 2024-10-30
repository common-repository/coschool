<?php
/**
 * The Template for displaying course archives, including the main courses page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/coschool/archive-course.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */
use Codexpert\CoSchool\Helper;

defined( 'ABSPATH' ) || exit;

get_header( 'courses' );

/**
 * Hook: coschool_before_main_content.
 */
do_action( 'coschool_before_main_content' );

?>
<header class="coschool-courses-header">
	<?php
	/**
	 * Hook: coschool_archive_description.
	 */
	do_action( 'coschool_archive_description' );
	?>
</header>
<?php
if ( have_posts() ) {

	/**
	 * Hook: coschool_before_courses_loop.
	 */
	do_action( 'coschool_before_courses_loop' );
	
	?>
	<div id="coschool-courses">
	<?php
	while ( have_posts() ) {
		the_post();

		/**
		 * Hook: coschool_courses_loop.
		 */
		do_action( 'coschool_courses_loop' );
	}
	?>
	</div>
	<?php

	/**
	 * Hook: coschool_after_courses_loop.
	 */
	do_action( 'coschool_after_courses_loop' );
}
else {
	/**
	 * Hook: coschool_no_courses_found.
	 */
	do_action( 'coschool_no_courses_found' );
}

/**
 * Hook: coschool_after_main_content.
 */
do_action( 'coschool_after_main_content' );

/**
 * Hook: coschool_sidebar.
 */
do_action( 'coschool_sidebar' );

get_footer( 'courses' );