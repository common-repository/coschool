<?php 
global $quiz_data;

$question_ids 	= $quiz_data->list_questions();

if ( $question_ids == ''  ) {
	echo "There are no questions in this quiz.";
	return;
}

$question_ids 	= $question_ids ? $question_ids : [];
$config 		= $quiz_data->get_config( 'coschool_quiz_config_retake' );
$attempts 		= $quiz_data->get_attempts( get_current_user_id() );
$is_eligible	= true;
$form_inputs 	= '<input type="hidden" name="action" value="coschool-start-quiz">'.wp_nonce_field( 'coschool' , '_wpnonce', true, false );
$last_attempt 	= $attempts ? end( $attempts ) : [];
$retake_delay 	= $attempts ? $last_attempt->time : 0;

if ( isset( $config['retake_delay_d'] ) && $config['retake_delay_d'] != '' ) {
	$retake_delay += DAY_IN_SECONDS * $config['retake_delay_d'];
}
if ( isset( $config['retake_delay_h'] ) && $config['retake_delay_h'] != '' ) {
	$retake_delay += HOUR_IN_SECONDS * $config['retake_delay_h'];
}
if ( isset( $config['retake_delay_m'] ) && $config['retake_delay_m'] != '' ) {
	$retake_delay += MINUTE_IN_SECONDS * $config['retake_delay_m'];
}
if ( isset( $config['retake_delay_s'] ) && $config['retake_delay_s'] != '' ) {
	$retake_delay += $config['retake_delay_s'];
}
if ( time() < $retake_delay ) {
	$is_eligible 	= false;
}

$retake_time = wp_date( 'M d,Y H:i:s', $retake_delay );
if( $quiz_data->has_access() ) :
	if( ! $is_eligible ) :
	 ?>
		 <script>
	 	jQuery(function($) {
			 // Set the date we're counting down to
			 var countDownDate = new Date('<?php echo $retake_time; ?>').getTime();

			 // Update the count down every 1 second
			 var x = setInterval(function() {
			   var now = new Date().getTime();
			   var distance = countDownDate - now;
			   var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			   var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			   var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			   var seconds = Math.floor((distance % (1000 * 60)) / 1000);

			   if ( hours < 10 ) hours = '0'+hours;
			   if ( minutes < 10 ) minutes = '0'+minutes;
			   if ( seconds < 10 ) seconds = '0'+seconds;
			   
			   $("#coschool-retake-timer").html( days + "d " + hours + "h " + minutes + "m " + seconds + "s " );
			   if (distance < 0) {
			    	clearInterval(x);
			 		$("#coschool-quiz-start-form").append( '<?php echo $form_inputs; ?>' );
			     	$('#coschool-quiz-start-form button').attr( 'disabled', false );
			     	$("#coschool-retake-timer-container").html('')
			   }
			 }, 1000);
		})
		 </script>
<?php 
	endif;
?>
<form id="coschool-quiz-start-form" method="post">
	<div class="coschool-quiz-content">
		<h2><?php esc_html_e( 'Instruction', 'coschool' ); ?></h2>
		<?php 
		the_content(); 

		echo $is_eligible && $question_ids ? $form_inputs : '';
		?>
		<input type="hidden" name="quiz_id" value="<?php esc_attr_e( $quiz_data->get('id') ); ?>">
		<div class="coschool-quiz-buttton-panel">
			<button <?php echo ! $is_eligible || ! $question_ids ? 'disabled=""' : ''; ?> type="submit"><?php esc_html_e( 'Start Quiz', 'coschool' ); ?></button>
			<?php 
			if( ! $is_eligible ){
				echo "<span id='coschool-retake-timer-container'>";
				printf( __( 'You will be eligible for this quiz again after %s', 'coschool' ), '<b><span id="coschool-retake-timer">0d 0h 0m 0s</span></b>' );
				echo "</span>";
			}
			?>			 
		</div>
	</div>
</form>
<?php 
	else: 
		echo '<div class="coschool-quiz-no-access">';
		the_content();
		echo '</div>';
	endif;
 ?>