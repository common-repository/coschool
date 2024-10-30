<?php
/**
 * Courses shortcode
 * 
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\Instructor\Data as Instructor_Data;

$courses =  Helper::get_posts( [ 
	'post_type' 		=> 'course',
	'post_status' 		=> 'any', 
	'posts_per_page'	=> $args['count'],
	'author' 			=> $args['instructor'],
	'course-category'	=> $args['category'],
	'meta_query'		=> [
		[
			'key' 		=> 'coschool_pricing',
			'value'		=> $args['type'],
			'compare' 	=> 'LIKE'
		]
	]

] );
?>

<div class="course-layout-toggle-btn">
	<i class="fas fa-th"></i>
	<i class="fas fa-th-list" style="display: none;"></i>
</div>
<div id='coschool-courses' class='coschool'>

<?php
foreach ( $courses as  $id => $title ) {

	$course_data 		= new Course_Data( $id );
	$instructor_data	= new Instructor_Data( $course_data->get( 'author' ) );
	$permalink   		= get_permalink( $id );
	?>

	<div id='course-<?php esc_attr_e( $id ); ?>' class='course-card'>
		<div class="course-header">
			<div class="course-thumbnail">
				<a href="<?php esc_attr_e( $permalink ); ?>">
					<?php echo wp_kses_post( $course_data->get_thumbnail( 'coschool-thumb' ) ); ?>
				</a>
			</div>
		</div>
		<div class="course-content">
			<h2 class="course-title"><a href="<?php esc_attr_e( $permalink ); ?>"><?php esc_html_e( $title ) ?></a></h2>
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
			<a class="course-enrol-btn" href="<?php echo esc_url( $course_data->get( 'purchase_url' ) ) ?>"><?php echo $course_data->get( 'enroll_label' ); ?></a>
		</div>
	</div>
	<?php
}

echo "</div>";


