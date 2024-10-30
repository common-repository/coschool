<?php 
use Codexpert\CoSchool\App\Question\Data as Question_Data;
use Codexpert\CoSchool\App\Quiz\Data as Quiz_Data;
use Codexpert\CoSchool\Helper;
global $quiz_data;

if ( ( !isset( $quiz_Data ) || ! $quiz_Data ) && isset( $args['quiz_id'] ) && $args['quiz_id'] != '' ) {
	$quiz_data = new Quiz_Data( coschool_sanitize( $args['quiz_id'] ) );
}

if ( ! $quiz_data  ) return;

$question_ids 		= $quiz_data->list_questions();
$config 			= $quiz_data->get_config();
$quiz_time_config 	= $quiz_data->get_config( 'coschool_quiz_config_time' );
$quiz_time_enabled 	= isset( $quiz_time_config['enable_quiz_time'] );
$single_question	= '';

if ( ! isset( $config['question_at_once'] ) ) {
	$single_question = 'coschool-single-question';
}
?>

<div id="coschool-floating-counter">
	<div class="coschool-quiz-progress-content">
		<h4><?php
			echo sprintf( __( 'Answered: %s/%s', 'coschool' ), "<span class='coschool-answered-questions'>0</span>", "<span id='coschool-total-questions'>" . count( $question_ids ) . "</span>" );
		?></h4>
		<?php if ( $quiz_time_enabled ): ?>
		<h4 class="coschool-quiz-timer">00:00:00</h4>
		<?php endif; ?>
	</div>
</div>
<div class="coschool-quizzes <?php esc_attr_e( $single_question ); ?>">
	<form id="coschool-quiz-form" action="" method="post">
		<div class="coschool-quiz">
			<?php 
	
			if ( isset( $config['randomize_questions'] ) && 'on' == $config['randomize_questions'] ) {
				shuffle( $question_ids );
			}
			
			foreach ( $question_ids as $question_id ): 
				$question_data 	= new Question_Data( $question_id );
				$question_type 	= $question_data->get('question_type');
				$is_required 	= $question_data->get('question_required');
				$required_txt 	= $is_required == 'yes' ? '<span class="coschool-required-question">*</span>' : '';
				$required_attr  = $is_required == 'yes' ? 'required' : '';
				?>
				<div class="coschool-quiz-question">
					<h4><?php echo sprintf( __( ' %s %s', 'coschool' ), $question_data->get('title'), $required_txt ) ?></h4>
					<div class="coschool-quiz-answer <?php esc_attr_e( $question_type );  echo " "; esc_attr_e( $required_attr ); ?>" >
						<?php 
						if ( in_array( $question_type, [ 'mcq', 'true_false' ] ) ):
							$options 		= $question_data->get( 'question_options' );
							$type 			= $question_type == 'mcq' ? 'checkbox' : 'radio';
							
							if ( $options && !empty( $options ) ) :
								foreach ( $options as $option ) :
									echo '
										<label class="coschool-quiz-label">' . esc_html( $option ) . '
											<input type="' . esc_attr( $type ) . '" name="answer['. esc_attr( $question_id ) .'][]" value="' . esc_attr( $option ) . '">
											<span class="coschool-quiz-checkmark"></span>
										</label>
									';
								endforeach;
							endif;
						elseif ( $question_type == 'text' ) :
							echo '<input type="text" name="answer['. esc_attr( $question_id  ).']" >';
						else:
							echo '<textarea name="answer['. esc_attr( $question_id ) .']"></textarea>';
						endif;
						 ?>
					</div>
					<div class='coschool-quiz-message'><?php  _e( 'This Question Is Required', 'coschool' ) ?></div>
				</div>
			<?php
			endforeach; 
			wp_nonce_field( 'coschool' );
			$start_time = time();
			if ( isset( $_COOKIE['coschool_qst'] ) ) $start_time = coschool_sanitize( $_COOKIE['coschool_qst'] )/1000;
			if ( ! isset( $config['question_at_once'] ) ) {
				?>
				<div class='coschool-next-question-wrapper'>
					<button type="button" id="prev-btn">
						<span class="dashicons dashicons-arrow-left-alt"></span>
						<?php esc_html_e( "Previous", 'coschool' ); ?>
					</button>
					<button type="button" id="next-btn">
						<?php esc_html_e( "Next", 'coschool' ); ?>
						<span class="dashicons dashicons-arrow-right-alt"></span>
					</button>
					<div class="coschool-quiz-buttton-panel show-submit">
						<button type="submit"><?php esc_html_e( 'Submit Answer', 'coschool' ); ?></button>
					</div>
				</div>
				<?php
			}
			?>
			<input type="hidden" name="start_time" value="<?php echo (int)$start_time; ?>">
			<input type="hidden" name="quiz_id" value="<?php esc_attr_e( $quiz_data->get( 'id' ) ); ?>">
			<input type="hidden" name="action" value="coschool-quiz-submit">
			<div class="coschool-quiz-buttton-panel">
				<button type="submit"><?php esc_html_e( 'Submit Answer', 'coschool' ); ?></button>
			</div>
		</div>
	</form>
</div>