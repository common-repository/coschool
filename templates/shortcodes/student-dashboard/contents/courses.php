<?php
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Instructor\Data as Instructor_Data;

$student_data 	= new Student_Data( get_current_user_id() );
$my_courses 	= $student_data->get_courses();
$course_url 	= coschool_dashoard_endpoint( 'courses' );

$payment_id 	= isset( $_GET['payment'] ) ? $_GET['payment'] : ''; //@TODO use filter courses by payment_id

$paginated 		= isset( $_GET['paginated'] ) ? coschool_sanitize( $_GET['paginated'] ) : 1;
$limit 			= 6;
$offset 		= $limit * ( $paginated - 1 );
$total 			= $page_count = count( $my_courses );
$paginat_number = ceil( $total / $limit );
$my_courses 	= array_slice( $my_courses, $offset, $limit );
?>

<div class="coschool-dashboard-header">
	<h2 class="coschool-dashboard-title"><?php _e( 'My Courses', 'coschool' ); ?></h2>
		<?php if ( count( $my_courses ) == 0 ): ?>
			<h6 class="coschool-dashboard-subtitle"><?php _e( 'You did not enrolled to any course.', 'coschool' ); ?></h6>
		<?php else: ?>
			<h6 class="coschool-dashboard-subtitle"><?php _e( 'Your courses list.', 'coschool' ); ?></h6>
		<?php endif ?>
</div>
<div class="coschool-dashboard-body">
	<div class="coschool-dashboard-mycourses">
		<?php
		if ( ! empty( $my_courses ) ) : 
			
			foreach ( $my_courses as $course_id ) :
				$course 		= new Course_Data( $course_id );
				$instructor 	= new Instructor_Data( $course->get_instructor() );
				$lessons 		= $course->get_lessons();
				$quizzes 		= $course->get_quizzes();
				$assignments 	= $course->get_assignments();
				$duration 		= $course->get_duration();
				$progress 		= $student_data->get_progress( $course_id );

				$title 			= $course->get('title');
				$link 			= get_permalink( $course->get( 'id' ) );
				$thumbnail 		= $course->get_thumbnail( 'coschool-small-thumb' );

				$is_completed 	= $student_data->has_completed( $course_id );
				$lesson_url 	= $student_data->get_last_content( $course_id, true ) ? : get_permalink( $course_id );
				$course_review	= get_user_meta( $course_id,'course_review' );


				$student_reviewed 	= $student_data->get( "reviewed_{$course_id}", 1 );
				$certificate_config = $course->get( 'coschool_certification' );
				$certificate_status = isset( $certificate_config['enable_certificate'] ) ? $certificate_config['enable_certificate'] : 'no';
 		?>
			<div class="coschool-dashboard-mycourse">
				<div class="coschool-dm-img">
					<a href="<?php echo esc_url( $link ); ?>">
						<?php echo wp_kses_post( $thumbnail ); ?>
					</a>
				</div>
				<div class="coschool-dm-content">
					<h3 class="coschool-dm-title"><a href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( $title ) ?></a></h3>
					<div class="coschool-dm-course-info">
						<div class="coschool-dm-course-author">
							<img src="<?php echo esc_url( $instructor->get_avatar_url() ); ?>" alt="<?php esc_attr_e( $instructor->get( 'name') ) ?>">
							<?php esc_html_e( $instructor->get( 'name') ); ?>
						</div>
						<div class="coschool-dm-course-lessons">
							<?php echo sprintf( __( '%s Lessons %s', 'coschool' ), '<i class="fas fa-book-open"></i>', count( $lessons ) ); ?>
						</div>
						<div class="coschool-dm-course-quiz">
							<?php echo sprintf( __( '%s Quizzes %s', 'coschool' ), '<i class="far fa-question-circle"></i>', count( $quizzes ) ); ?>
						</div>
						<div class="coschool-dm-course-assignemnt">
							<?php echo sprintf( __( '%s Assignments %s', 'coschool' ), '<i class="far fa-file-alt"></i>', count( $assignments ) ); ?>
						</div>
						<div class="coschool-dm-course-time">
							<?php echo sprintf( __( '%s %s', 'coschool' ), '<i class="far fa-clock"></i>', $duration ); ?>
						</div>
					</div>
					<div class="coschool-dm-progress">
						<div class="coschool-dm-progress-bar">
							<div class="coschool-dm-progress-bar-innner" style="width: <?php echo esc_attr( $progress ); ?>%;"></div>
						</div>
						<div class="coschool-dm-progress-count"><?php echo esc_attr( $progress ); ?>%</div>
					</div>
				</div>
				<div class="coschool-dm-action">

					<?php if ( $is_completed ) : ?>

							<?php if ( $certificate_status == 'yes' ): ?>
								<button data-course_id="<?php esc_attr_e( $course_id ); ?>" class="coschool-dm-certificate-btn download button-one"><i class="fas fa-crown"></i> <?php _e( 'Get Certificate', 'coschool' ) ?></button>
							<?php endif ?>

							<?php if ( $student_reviewed  == 0  ): ?>
								<button data-course_id="<?php esc_attr_e( $course_id ); ?>" class="coschool-course-dashboard-my-course-btn completed button-two"><?php _e( 'Give Feedback', 'coschool' ) ?></button>
							<?php elseif(  $student_reviewed  == 1  ) : ?>
								<button disabled class="coschool-course-dashboard-my-course-btn button-two"><?php _e( 'Already Reviewed', 'coschool' ) ?></button>
							<?php endif ?>
						<a  class="coschool-dm-certificate-btn button-one " href="<?php echo esc_url( $lesson_url ) ?>"><?php _e( 'Continue Reading' ) ?></a>
					<?php else: ?>
						<a class="coschool-dm-certificate-btn button-one " href="<?php echo esc_url( $lesson_url ) ?>"><?php _e( 'Continue Reading' ) ?></a>
					<?php endif; ?>
				</div>
			</div>
		<?php 
			endforeach;
		else:
		endif; ?>
	</div>

	<div class="coschool-my-course-review-modal" style="display: none;">
	<div class="coschool-my-course-review-form">
		<form action="">
			<?php wp_nonce_field( 'coschool' ); ?>
			<input type="hidden" name="action" value="course-review">
			<input id="course_id" type="hidden" name="course_id" value="0">
			<h2><?php _e( 'Rate Your Experience', 'coschool' ); ?></h2>
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
				<label for=""><?php _e( 'How was your Experience?', 'coschool' ); ?></label>
				<textarea name="comment" id="" cols="4" rows="4"></textarea>
			</p>
			<p class="coschool-review-action">
				<button class="coschool-submit-course-review" type="submit"><?php _e( 'Publish Review', 'coschool' ); ?></button>
				<button class="coschool-review-modal-close"><?php _e( 'Maybe Letter', 'coschool' ); ?></button>
			</p>
		</form>
		<button class="coschool-review-modal-close coschool-modal-close"><i class="fas fa-times"></i></button>
	</div>
</div>

	<div class="coschool-dashboard-pagination">
		<?php 
			if ( $page_count > $limit ) {
				coschool_paginated( $course_url, $page_count, $paginat_number ); 
			}
		?>
	</div>
</div>