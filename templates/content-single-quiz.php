<?php
namespace Codexpert\CoSchool\App;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\Helper;
/**
 * The Template for displaying the content of a single quiz
 *
 * This template can be overridden by copying it to yourtheme/coschool/content-single-quiz.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $quiz_data, $student_data, $course_data;
$question_ids 		= $quiz_data->list_questions();
$question_ids 		= $question_ids ? $question_ids : [];
$config 			= $quiz_data->get_config();
$quiz_time_config 	= $quiz_data->get_config( 'coschool_quiz_config_time' );
$quiz_time_enabled 	= isset( $quiz_time_config['enable_quiz_time'] );
$quiz_retake 		= $quiz_data->get_config( 'coschool_quiz_config_retake' );
$quiz_deadline		= $quiz_data->get_config( 'coschool_quiz_config_deadline' );
$deadline_enabled	= isset( $quiz_deadline['enable_dead_line'] ) && $quiz_deadline['enable_dead_line'] == 'on';
$quiz_time  	  	= 0;
$deadline_time  	= 0;
$quiz_duration 		= '00h:00m:00s';
$quiz_step 			= 1;
$attempts 			= $quiz_data->get_attempts( get_current_user_id() );
$is_completed		= false;
$introduction_html	= Helper::get_view( 'introduction', 'templates/quiz' );
$questions_html 	= $result_html = '';
$prerequisites 		= $student_data->has_prerequisites( $quiz_data->get( 'id' ) );
$has_completed 		= $student_data->has_completed( $quiz_data->get( 'id' ) );
$course_url 		= get_the_permalink( $course_data->get( 'id' ) );

if ( $quiz_time_enabled ) {
	$_quiz_duration = [];
	if ( isset( $quiz_time_config['quiz_time_h'] ) && '' != $quiz_time_config['quiz_time_h'] ) {
		$quiz_time += HOUR_IN_SECONDS * $quiz_time_config['quiz_time_h'] ;
		$_quiz_duration[] = $quiz_time_config['quiz_time_h'] . 'h';
	}
	if ( isset( $quiz_time_config['quiz_time_m'] ) && '' != $quiz_time_config['quiz_time_m'] ) {
		$quiz_time += MINUTE_IN_SECONDS * $quiz_time_config['quiz_time_m'] ;
		$_quiz_duration[] = $quiz_time_config['quiz_time_m'] . 'm';
	}
	if ( isset( $quiz_time_config['quiz_time_s'] ) && '' != $quiz_time_config['quiz_time_s'] ) {
		$quiz_time += $quiz_time_config['quiz_time_s'];
		$_quiz_duration[] = $quiz_time_config['quiz_time_s'] . 's';
	}
	$quiz_duration = implode( ':', $_quiz_duration );
}

if ( $deadline_enabled ) {
	$deadline_time = '';
	if ( isset( $quiz_deadline['quiz_deadline_d'] ) && '' != $quiz_deadline['quiz_deadline_d'] ) {
		$deadline_time .=  $quiz_deadline['quiz_deadline_d'] . " ";
	}
	if ( isset( $quiz_deadline['quiz_deadline_t'] ) && '' != $quiz_deadline['quiz_deadline_t'] ) {
		$deadline_time .= $quiz_deadline['quiz_deadline_t'];
	}
}

if ( ( $deadline_enabled && strtotime( $deadline_time ) < time() && count( $attempts ) > 0  ) ||
	( count( $attempts ) > 0 && ! isset( $quiz_retake['enable_retake'] ) ) || 
	( isset( $quiz_retake['enable_retake'] ) && isset( $quiz_retake['quiz_retake_time'] ) && count( $attempts ) >= $quiz_retake['quiz_retake_time'] )
   ){
   	$introduction_html	= $questions_html 	= $result_html = '';


		$last_attempt 		= end( $attempts );
		$last_attempt_id 	= $last_attempt->id;
		$quiz_id 			= $quiz_data->quiz->id;

		if( quiz_attempt_status( $last_attempt_id ) == 'passed' ){
            $result_html = Helper::get_view( 'passed', 'templates/quiz', [ 'quiz_id' => $quiz_id ] );
		}
		elseif( quiz_attempt_status( $last_attempt_id ) == 'failed'  ){
			$result_html = Helper::get_view( 'failed', 'templates/quiz', [ 'quiz_id' => $quiz_id ] );
		}
		else{
			$result_html = Helper::get_view( 'result', 'templates/quiz', [ 'quiz_id' => $quiz_id ] );
		}
	
	$is_completed = true;
}
else if ( isset( $_COOKIE['quiz_screen'] )  && $_COOKIE['quiz_screen'] == 'questions' ) {
	$introduction_html	= $questions_html 	= $result_html = '';
	$questions_html 	= Helper::get_view( 'questions', 'templates/quiz' );
}

$progress_bar = $is_completed ? '100%' : '0%';
?>
<div id="quiz-<?php the_ID(); ?>" class="coschool-singular-grid">
	<div class="coschool-quiz-details">
		<h2 class="coschool-parent-course-title"><a href="<?php echo esc_url( $course_url ); ?>"><?php esc_html_e( $course_data->get('title') ); ?></a></h2>
		<h1 class="coschool-singular-title"><?php the_title(); ?></h1>
		<?php if ( coschool_has_access( get_the_ID() ) && !$prerequisites ): ?>
		<p class="coschool-quiz-desc"></p>

		<div class="coschool-quiz-instruction">

			<div class="coschool-quiz-metas coschool-meta">
				<div class="coschool-meta-item"><?php echo esc_url( coschool_get_icon( 'question' ) ); ?><?php echo sprintf( __( '%d Questions', 'coschool' ), count( $question_ids ) ); ?></div>

				<?php if ( isset( $quiz_deadline['enable_dead_line'] ) && 'on' == $quiz_deadline['enable_dead_line'] ) : ?>
					<div class="coschool-meta-item">
						<span><i class="fas fa-calendar-week"></i></span>
						<?php 
						$date = wp_date( 'D, M d, Y', time() + WEEK_IN_SECONDS );
						$time = '11:59 PM';

						if ( isset( $quiz_deadline['quiz_deadline_d'] ) && '' != $quiz_deadline['quiz_deadline_d'] ) {
							$date = wp_date( 'D, M d, Y', strtotime( $quiz_deadline['quiz_deadline_d'] ) );
						}

						if ( isset( $quiz_deadline['quiz_deadline_t'] ) && '' != $quiz_deadline['quiz_deadline_t'] ) {
							$time = date( 'h:i a', strtotime( $date .' '. $quiz_deadline['quiz_deadline_t'] ) );
						}

						echo sprintf( __( 'Due %s by %s', 'coschool' ), $date, $time ); ?>
					</div>
				<?php endif; ?>

				<?php if ( $quiz_time_enabled ) : ?>	
				<div class="coschool-meta-item"><span><i class="far fa-clock"></i></span><?php esc_html_e( $quiz_duration ); ?></div>
				<?php endif; ?>

				<div class="coschool-meta-item coschool-content-status <?php echo $has_completed ? 'completed' : ''; ?>">
					<span><?php echo $has_completed ? esc_html_e( 'Completed', 'coschool' ) : esc_html_e( 'Incomplete', 'coschool' ); ?></span>
				</div>
			</div>
			
			<div class="coschool-quiz-progress-bar-panel">
				<div class="coschool-quiz-progress-content">
					<h4><?php
						echo sprintf( __( 'Answered: %s/%s', 'coschool' ), "<span class='coschool-answered-questions'>0</span>", "<span id='coschool-total-questions'>" . count( $question_ids ) . "</span>" );
					?></h4>
					<?php if ( $quiz_time_enabled ): ?>
					<h4 id="coschool-quiz-timer" class="coschool-quiz-timer">00:00:00</h4>
					<?php endif; ?>
				</div>
				<div id="coschool-quiz-progress-bar" class="coschool-quiz-progress-bar <?php echo !$quiz_time_enabled ? 'answer_progress' : ''; ?>" style="width: <?php esc_attr_e( $progress_bar ); ?>"></div>
			</div>
			
		</div>
		<div id="coschool-quiz-instruction"><?php echo $introduction_html; ?></div>
		<div id="coschool-quiz-questions"><?php echo $questions_html; ?></div>
		<div id="coschool-quiz-result"><?php echo $result_html; ?></div>

		<?php elseif ( coschool_has_access( get_the_ID() ) && $prerequisites ) : ?>
		<div class="coschool-prerequisites-container">
			<?php do_action( 'coschool_prerequisites', $prerequisites, $quiz_data ); ?>
		</div>
		<?php else: the_content(); ?>
		<?php endif; ?>
	</div>
	
	<div class="coschool-course-progress">
		<h3 class="coschool-course-progress-title"><?php esc_html_e( 'Course Content', 'coschool' ) ?></h3>
		<?php do_action( 'coschool_course_progress', $quiz_data->get_course() ); ?>
	</div>
</div>