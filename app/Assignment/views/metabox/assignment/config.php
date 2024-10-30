<?php
use Codexpert\CoSchool\App\Assignment\Data;

global $post;

$assignment_data	= new Data( $post->ID );
$assignment_course	= $assignment_data->get( 'course_id' );

// print_r($assignment_data);
?>

<p>
	<label for="assignment-course"><?php _e( 'Course: ', 'coschool' ); ?></label>
	<?php
	if( $assignment_course != '' ) {
		printf( '<a href="%s">%s</a>', get_edit_post_link( $assignment_course ), get_the_title( $assignment_course ) );
	}
	else {
		_e( '[Not assigned]', 'coschool' );
	}
	?>
</p>