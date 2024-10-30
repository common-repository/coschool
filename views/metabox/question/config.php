<?php
use Codexpert\CoSchool\App\Question\Data;

global $post;

$question_data	= new Data( $post->ID );
$question_quiz	= $question_data->get( 'quiz_id' );
?>

<p>
	<label for="question-quiz"><?php _e( 'Quiz: ', 'coschool' ); ?></label>
	<?php
	if( $question_quiz != '' ) {
		printf( '<a href="%s">%s</a>', get_edit_post_link( $question_quiz ), get_the_title( $question_quiz ) );
	}
	else {
		_e( '[Not assigned]', 'coschool' );
	}
	?>
</p>