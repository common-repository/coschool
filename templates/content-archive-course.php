<?php
namespace Codexpert\CoSchool\App;
/**
 * The Template for displaying the content of course archive
 *
 * This template can be overridden by copying it to yourtheme/coschool/content-archive-course.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$course_data		= new Course\Data( get_the_ID() );
$instructor_data	= new Instructor\Data( $course_data->get( 'author' ) );
$student_data 		= new Student\Data( get_current_user_id() );
?>

<div id="course-<?php esc_attr_e( $course_data->get( 'id' ) ); ?>" class="course-card">
	<div class="course-header">
		<div class="course-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php echo wp_kses_post( $course_data->get_thumbnail( 'coschool-thumb' ) ); ?>
			</a>
		</div>
	</div>
	<div class="course-content">
		<h2 class="course-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
		<div class="course-author">
			<div class="course-author-image">
					<?php echo '<img class="course-author-thumbnail" alt="' . esc_attr( $instructor_data->get( 'name' ) ) . '" src="' . esc_url( $instructor_data->get_avatar_url() ) . '">'; ?>
			</div>
			<div class="course-author-name">
				<?php printf( __( 'By <a href="%s">%s</a>', 'coschool' ), esc_url( $instructor_data->get( 'archive_url' ) ), esc_html( $instructor_data->get( 'name' ) ) ); ?>
			</div>
			<div class="course-category">
				<?php if ( $course_cats = get_the_term_list( $course_data->get( 'id' ), 'course-category', '', ',' ) ) printf( __( 'in %s' ), $course_cats ); ?>
			</div>
		</div>
		<div class="course-summery">
			<div class="course-rating"><?php echo coschool_populate_stars( $course_data->get( 'rating' ) ); ?></div>
			<div class="course-price"><?php echo coschool_price( $course_data->get_price() ); ?></div>
		</div>
	</div>
	
	<div class="course-footer">
		<a class="course-enrol-btn" href="<?php echo esc_url( $course_data->get( 'purchase_url' ) ) ?>"><?php esc_html_e( $course_data->get( 'enroll_label' ) ); ?></a>
	</div>
</div>