<?php
namespace Codexpert\CoSchool\App;
/**
 * The Template for displaying the content of a single lesson
 *
 * This template can be overridden by copying it to yourtheme/coschool/content-single-lesson.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $lesson_data, $course_data, $student_data;

$lesson_tabs = [
	'content' 		=> __( 'Content', 'coschool' ),
	'discussion' 	=> __( 'Discussion', 'coschool' ),
];
$lesson_tabs = apply_filters( 'coschool_lesson-tabs', $lesson_tabs );

$is_completed 	= $student_data->has_completed( $lesson_data->get( 'ID' ) ) ? 'coschool-green' : 'coschool-ash';
$type_text 		= $student_data->has_completed( $lesson_data->get( 'ID' ) ) ? __( 'Completed', 'coschool' ) : __( 'Incomplete', 'coschool' );
$prerequisites 	= $student_data->has_prerequisites( $lesson_data->get( 'id' ) );
$course_url 	= get_the_permalink( $course_data->get( 'id' ) );

?>
<div id="coschool-lesson-<?php the_ID(); ?>" class="coschool-lesson-single coschool-singular-grid">
	<div class="coschool-ls-details">
		<h2 class="coschool-parent-course-title"><a href="<?php echo esc_url( $course_url ); ?>"><?php esc_html_e( $course_data->get('title') ); ?></a></h2>
		<h1 class="coschool-ls-title coschool-title"><?php the_title(); ?></h1>
		<?php if ( coschool_has_access( get_the_ID() ) && ! $prerequisites ): ?>
		<div class="coschool-ls-tags coschool-meta">
			<div class="coschool-meta-item coschool-ls-author">
				<?php
					echo wp_kses_post( $lesson_data->get_author_avatar() );
					esc_html_e( $lesson_data->get_author_name() );
				?></div>
			<div class="coschool-meta-item coschool-ls-duration"><span><i class="far fa-clock"></i></span><?php esc_html_e( $course_data->get_duration() ); ?></div>
			<div class="coschool-meta-item coschool-content-status <?php esc_attr_e( $is_completed ); ?>">
				<?php echo "<span class='" . esc_attr( $is_completed ) . "'>" . esc_html( $type_text ) . "</span>"; ?>
			</div>
		</div>
		<div class="coschool-ls-thumbnail">
			<?php echo wp_kses_post( $lesson_data->get_banner() ); ?>
		</div>
		
		<div class="coschool-ls-tabs coschool-tabs">
			<div class="coschool-ls-tab-items coschool-tab-items">
				<ul>
				<?php 
				$fisrt_item = array_key_first( $lesson_tabs );
				foreach ( $lesson_tabs as $key => $label ) {
					$active = $fisrt_item == $key ? 'active' : ''; 
					echo '<li class="coschool-ls-tab-item coschool-tab-item '. esc_attr( $active ) . '" data-tab="'. esc_attr( $key ) .'">'. esc_html( $label ) .'</li>';
				}
				?>
				</ul>
			</div>
			<div class="coschool-ls-tab-content coschool-tab-contents">
				<?php 
				foreach ( $lesson_tabs as $key => $label ) {
					$active = $fisrt_item == $key ? 'active' : ''; 
					echo "<div id='coschool-tab-content-" . esc_attr( $key ) . "' class='coschool-tab-content " . esc_attr( $active ) . "'>";
					do_action( "coschool_lesson_tab_content_{$key}", get_the_ID() );
					echo "</div>";
				}
				?>
			</div>
		</div>
		<?php elseif ( coschool_has_access( get_the_ID() ) && $prerequisites ) : ?>
		<div class="coschool-prerequisites-container">
			<?php do_action( 'coschool_prerequisites', $prerequisites, $lesson_data ); ?>
		</div>
		<?php else: the_content(); ?>
		<?php endif; ?>
	</div>

	<div class="coschool-ls-progress">

		<h3 class="coschool-course-progress-title"><?php esc_html_e( 'Course Content', 'coschool' ) ?></h3>
		<?php do_action( 'coschool_course_progress', $lesson_data->get_course() ); ?>
		
		<?php if( !$prerequisites && coschool_has_access( $lesson_data->get( 'id' ) ) && ! $student_data->has_completed( $lesson_data->get( 'id' ) ) && $student_data->has_course( $course_data->get( 'id' ) ) ) : ?>
		<div class="coschool-ls-mark-complete-section">
			<button class="coschool-mark-complete" data-content_id="<?php esc_attr_e( $lesson_data->get( 'id' ) ); ?>"><?php esc_html_e( 'Mark Complete', 'coschool' ); ?></button>
		</div>
		<?php endif; ?>

	</div>
</div>