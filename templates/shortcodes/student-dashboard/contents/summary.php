<?php
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;

$student_data 		= new Student_Data( get_current_user_id() );
$enrolled_courses 	= $student_data->get_courses();
$complete_courses 	= $student_data->get_courses( 'completed' );
$overall_progress 	= $student_data->get_overall_progress();

$cards = [
	'enrolled' => [
		'count' => count( $enrolled_courses ),
		'text' => __( 'Courses Enrolled', 'coschool' ),
		'icon' => '<i class="fas fa-book-open"></i>'
	],
	'progress' => [
		'count' => $overall_progress,
		'text' => __( 'Progress', 'coschool' ),
		'icon' => '<i class="fas fa-spinner"></i>'
	],
	'completed' => [
		'count' => count( $complete_courses ),
		'text' => __( 'Courses Completed', 'coschool' ),
		'icon' => '<i class="fas fa-trophy"></i>'
	],
	'payment' => [
		'count' => coschool_price( $student_data->get_spent(), false ),
		'text' => __( 'Total Spent', 'coschool' ),
		'icon' => '<i class="fas fa-dollar-sign"></i>'
	],
];
?>

<div class="coschool-dashboard-header">
	<h2 class="coschool-dashboard-title"><?php esc_html_e( 'Summary', 'coschool' ); ?></h2>
</div>
<div class="coschool-dashboard-body">
	<div class="coschool-dashboard-cards">
		<?php foreach ( $cards as $key => $card ): ?>
			<div id="coschool-dashboard-card-<?php esc_attr_e( $key ) ?>" class="coschool-dashboard-card">
				<div class="edc-left">
					<?php echo wp_kses_post( $card['icon'] ); ?>
				</div>
				<div class="edc-right">
					<div class="edc-count"><?php echo wp_kses_post( $card['count'] ); ?></div>
					<div class="edc-text"><?php esc_html_e( $card['text'] ); ?></div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<div class="coschool-dashboard-big-cards">
		<div class="coschool-dashboard-courses">
			<?php echo Helper::get_view( 'student-reports', 'views/front' ); ?>
		</div>
	</div>
</div>

<div class="coschool-my-course-review-modal" style="display: none;">
	<div class="coschool-my-course-review-form">
		<form action="">
			<?php wp_nonce_field( 'coschool' ); ?>
			<input type="hidden" name="action" value="course-review">
			<input id="course_id" type="hidden" name="course_id" value="0">
			<h2><?php esc_html_e( 'Rate Your Experience', 'coschool' ); ?></h2>
			<div class="coschool-my-course-rating">
	            <input id="star5" name="rating" type="radio" value="5" class="radio-btn hide" />
	            <label for="star5" >☆</label>
	            <input id="star4" name="rating" type="radio" value="4" class="radio-btn hide" />
	            <label for="star4" >☆</label>
	            <input id="star3" name="rating" type="radio" value="3" class="radio-btn hide" />
	            <label for="star3" >☆</label>
	            <input id="star2" name="rating" type="radio" value="2" class="radio-btn hide" />
	            <label for="star2" >☆</label>
	            <input id="star1" name="rating" type="radio" value="1" class="radio-btn hide" />
	            <label for="star1" >☆</label>
	            <div class="clear"></div>
	        </div>
			<p>
				<label for=""><?php esc_html_e( 'How was your Experience?', 'coschool' ); ?></label>
				<textarea name="comment" id="" cols="4" rows="4"></textarea>
			</p>
			<p class="coschool-review-action">
				<button class="coschool-submit-course-review" type="submit"><?php esc_html_e( 'Publish Review', 'coschool' ); ?></button>
				<button class="coschool-review-modal-close"><?php esc_html_e( 'Maybe Letter', 'coschool' ); ?></button>
			</p>
		</form>
		<button class="coschool-review-modal-close coschool-modal-close"><i class="fas fa-times"></i></button>
	</div>
</div>