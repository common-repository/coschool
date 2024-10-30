<?php
use Codexpert\CoSchool\App\Lesson\Data;

global $post;

$lesson_data	= new Data( $post->ID );
$lesson_course	= $lesson_data->get( 'course_id' );
?>

<p>
	<label for="lesson-course"><?php _e( 'Course: ', 'coschool' ); ?></label>
	<?php
	if( $lesson_course != '' ) {
		printf( '<a href="%s">%s</a>', get_edit_post_link( $lesson_course ), get_the_title( $lesson_course ) );
	}
	else {
		_e( '[Not assigned]', 'coschool' );
	}
	?>
</p>
<p>
	<label for="lesson-type"><?php _e( 'Free Lesson?', 'coschool' ); ?></label>
	<input type="checkbox" id="lesson-type" name="free_lesson" value="1" <?php checked( $lesson_data->get( 'free_lesson' ), 1, true ); ?>>
	<p class="description"><?php _e( 'Check this if you want to keep this lesson open for all even if it belongs to a paid course.', 'coschool' ); ?></p>
</p>