<?php 
global $post;

wp_enqueue_script( 'jquery-ui-sortable' );
wp_enqueue_style( 'coschool-admin-quiz' );
wp_enqueue_script( 'coschool-admin-quiz' );

$question_types = [
	'text' 			=> __( 'Text', 'coschool' ),
	'paragraph' 	=> __( 'Paragraph', 'coschool' ),
	'true_false'	=> __( 'True/False', 'coschool' ),
	'mcq' 			=> __( 'Multiple Choices', 'coschool' ),
];
$question_ids = get_post_meta( $post->ID, 'questions', true );
?>

<div class="quiz-question-wrap">
	<?php if ( !empty( $question_ids ) ) :
		$count = 1;
		foreach ( $question_ids as $question_id ) :
			$type 		= get_post_meta( $question_id, 'question_type', true );
			$options 	= get_post_meta( $question_id, 'question_options', true );
			$correct 	= get_post_meta( $question_id, 'question_correct', true );
			$mark 	 	= get_post_meta( $question_id, 'question_mark', true );
			$required 	= get_post_meta( $question_id, 'question_required', true );
			$required 	= $required && $required == 'yes' ? 'checked' : '';
			$correct 	= $correct ? $correct : [];
			$show_option_section = in_array( $type, [ 'mcq', 'true_false' ] ) && $options;
		?>
			<div class="quiz-question-set" data-count='<?php  esc_attr_e( $count ); ?>'>
				<input type="hidden" name="questions[<?php  esc_attr_e( $count ); ?>][id]" value="<?php  esc_attr_e( $question_id ); ?>">
				<div class="quiz-question-set-header">
					<div class="quiz-question-title">
						<div class="remove-quiz-question-set"><span class="dashicons dashicons-no-alt"></span></div>
						<label for="quiz-question"><?php _e( 'Question', 'coschool' ); ?></label>
						<input type="text" name="questions[<?php esc_attr_e( $count ); ?>][title]" value="<?php esc_attr_e( get_the_title( $question_id ) ); ?>">

					</div>
					<div class="quiz-question-type">
						<label for="quiz-question-type"><?php _e( 'Type', 'coschool' ) ?></label>
						<select name="questions[<?php esc_attr_e( $count ); ?>][type]">
							<?php foreach ( $question_types as $key => $label ){
								echo '<option ' . selected( $key, $type, false ) . ' value="'. esc_attr( $key ) .'">'. esc_html( $label ) .'</option>';
							} ?>
						</select>
					</div>
					<div class="quiz-question-mark">
						<label for="quiz-question"><?php esc_html_e( 'Mark', 'coschool' ); ?></label>
						<input type="number" name="questions[<?php esc_attr_e( $count ); ?>][mark]" value="<?php esc_attr_e( $mark ); ?>">
					</div>
					<div class="quiz-question-required">
						<label for="quiz-question"><?php _e( 'Required', 'coschool' ); ?></label>
						<label class="quiz-question-switch">
							  <input <?php esc_attr_e( $required ); ?> type="checkbox" name="questions[<?php esc_attr_e( $count ); ?>][is_required]">
							  <span class="slider round"></span>
						</label>
					</div>
				</div>
				<div class="quiz-question-options-section" <?php echo !$show_option_section ? 'style="display: none;"' : ''; ?>>
					<?php if ( $show_option_section ):
						echo '<div class="quiz-question-options">';
						$input_type 	= $type == 'mcq' ? 'checkbox' : 'radio';
						$removed_icon 	= $input_type == 'radio' ? '' : '<span class="dashicons dashicons-no-alt"></span>';
						foreach ( $options as $option ) :
							$checked = in_array( $option, $correct ) ? 'checked' : '';
							echo '
								<div class="quiz-question-option">
									<input title="' . __( 'Is this correct answer?', 'coschool' ) . '" type="'. esc_attr( $input_type ) .'" name="questions['. esc_attr( $count ) .'][correct][]" value="' . $option . '" class="quiz-question-correct" ' . $checked . '>
									<input type="text" name="questions['. $count .'][options][]" value="' . $option . '">
									' . $removed_icon . '
								</div>
							';
						endforeach;
						echo '</div>';
						if ( $type == 'mcq' ) {
							echo '<div class="quiz-question-option-btn">
									<button>' . __( '+ Add Option', 'coschool' ) . '</button>
								</div>';
						}
					 endif; ?>
				</div>
				<div class="quiz-question-space"></div>
			</div>
		<?php
		$count++;
		endforeach;
	endif; ?>
	<div class="quiz-question-add-new">
		<button id="quiz-add-new-question"><?php esc_attr_e( 'Add Question', 'coschool' ); ?></button>
	</div>
</div>

<!-- hidden Question set -->
<div id="quiz-question-set-hidden" style="display:none;">
	<div class="quiz-question-set" data-count='%%ques_set%%'>
		<div class="quiz-question-set-header">
			<div class="quiz-question-title">
				<div class="remove-quiz-question-set"><span class="dashicons dashicons-no-alt"></span></div>
				<label for="quiz-question"><?php esc_html_e( 'Question', 'coschool' ); ?></label>
				<input type="text" name="questions[%%ques_set%%][title]" >
			</div>
			<div class="quiz-question-type">
				<label for="quiz-question-type"><?php esc_html_e( 'Type', 'coschool' ); ?></label>
				<select name="questions[%%ques_set%%][type]">
					<?php foreach ( $question_types as $key => $label ){
						echo '<option value="'. esc_attr( $key ) .'">'. esc_html( $label ) .'</option>';
					} ?>
				</select>
			</div>
			<div class="quiz-question-mark">
				<label for="quiz-question"><?php esc_html_e( 'Mark', 'coschool' ); ?></label>
				<input type="number" name="questions[%%ques_set%%][mark]">
			</div>
			<div class="quiz-question-required">
				<label for="quiz-question"><?php esc_html_e( 'Required', 'coschool' ); ?></label>
				<label class="quiz-question-switch">
					  <input type="checkbox" name="questions[%%ques_set%%][is_required]">
					  <span class="slider round"></span>
				</label>
			</div>
		</div>
		<div class="quiz-question-options-section" style="display:none;"></div>
		<div class="quiz-question-space"></div>
	</div>
</div>

<!-- quiz options button-->
<div id="quiz-question-option-btn-hidden" style="display: none;">
	<div class="quiz-question-option-btn">
		<button><?php esc_html_e( '+ Add Option', 'coschool' ); ?></button>
	</div>
</div>