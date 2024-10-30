<?php
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Question\Data;

global $post;
$question_id = $post->ID;

$quizzes			= Helper::get_posts( [ 'post_type' => 'quiz' ] );
$question_data		= new Data( $question_id );
$current_quiz_id	= $question_data->get( 'quiz_id' );
?>

<p>
	<label for="question-quiz"><?php esc_html_e( 'Quiz', 'coschool' ); ?></label>
	<select id="question-quiz" name="quiz_id">
		<option value=""><?php esc_html_e( 'Select a Quiz', 'coschool' ); ?></option>
		<?php
		foreach ( $quizzes as $quiz_id => $quiz_name ) {
			echo "<option value='{$quiz_id}' " . selected( $quiz_id, $current_quiz_id, false ) . ">" . esc_html( $quiz_name ) ."</option>";
		}
		?>
	</select>
</p>