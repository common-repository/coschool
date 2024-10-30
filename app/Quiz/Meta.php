<?php
/**
 * All quiz related functions
 */
namespace Codexpert\CoSchool\App\Quiz;
use Codexpert\CoSchool\Helper;
use Codexpert\Plugin\Metabox as Metabox_API;
// use Codexpert\CoSchool\App\Quiz\Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Quiz
 * @author Codexpert <hi@codexpert.io>
 */
class Meta {

	/**
	 * Generates config metabox
	 * 
	 * @uses add_meta_box()
	 */
	public function question() {
		// add_meta_box( 'coschool-quiz-config', __( 'Configuration', 'coschool' ), [ $this, 'callback_course' ], 'quiz', 'normal', 'high' );
		add_meta_box( 'coschool-quiz-questions', __( 'Questions', 'coschool' ), [ $this, 'callback_question' ], 'quiz' );
	}

	public function callback_course() {
		echo Helper::get_view( 'config', 'views/metabox/quiz' );
	}

	public function callback_question() {
		echo Helper::get_view( 'question', 'views/metabox/quiz' );
	}


	public function config() {
		$lesson_args 		= [ 'post_type'	=> 'lesson'	];
		$quiz_args 			= [ 'post_type'	=> 'quiz'	];
		$assignment_args 	= [ 'post_type'	=> 'assignment'	];
		$current_user_id 	= get_current_user_id();

		if( ! current_user_can( 'edit_pages' ) ) {
			$lesson_args['author']		= $current_user_id;
			$quiz_args['author']		= $current_user_id;
			$assignment_args['author']	= $current_user_id;
		}

		$metabox = [
			'id'			=> 'coschool-quiz-settings',
			'label'			=> __( 'Quiz Configuration', 'coschool' ),
			'post_type'		=> 'quiz',
			'topnav'		=> wp_is_mobile(),
			'sections'		=> [
				'coschool_quiz_config'	=> [
					'id'        => 'coschool_quiz_config',
					'label'     => __( 'General', 'coschool' ),
					'icon'      => 'dashicons-admin-tools',
					'no_heading'=> false,
					'fields'    => [
						'pass_mark'		=> [
							'id'		=> 'pass_mark',
							'label'     => __( 'Pass Mark', 'coschool' ),
							'type'      => 'number',
							'default'	=> 40,
							// 'desc' 		=> __( 'Minimum number to ', 'coschool' ),
						],
						'randomize_questions'	=> [
							'id'		=> 'randomize_questions',
							'label'     => __( 'Randomize Questions', 'coschool' ),
							'type'      => 'switch',
							'desc' 		=> __( 'Randomize questions on view time', 'coschool' ),
						],
						'question_at_once'	=> [
							'id'		=> 'question_at_once',
							'label'     => __( 'Question at once', 'coschool' ),
							'type'      => 'switch',
							'desc' 		=> __( 'View question one by one or all together', 'coschool' ),
						],
					]
				],
				'coschool_quiz_prerequisites'	=> [
					'id'        => 'coschool_quiz_prerequisites',
					'label'     => __( 'Prerequisites', 'coschool' ),
					'icon'      => 'dashicons-shield-alt',
					'no_heading'=> false,
					'fields'    => [						
						'prerequisites_lesson'	=> [
							'id'		=> 'prerequisites_lesson',
							'label'     => __( 'Lessons', 'coschool' ),
							'type'      => 'select',
							'multiple'  => true,
							'chosen'    => true,
							'options'	=> Helper::get_posts( $lesson_args ),
							// 'desc' 		=> __( 'Randomize questions on view time', 'coschool' ),
						],
						'prerequisites_quiz'	=> [
							'id'		=> 'prerequisites_quiz',
							'label'     => __( 'Quizzes', 'coschool' ),
							'type'      => 'select',
							'multiple'  => true,
							'chosen'    => true,
							'options'	=> Helper::get_posts( $quiz_args ),
							// 'desc' 		=> __( 'Randomize questions on view time', 'coschool' ),
						],
						'prerequisites_assignment'	=> [
							'id'		=> 'prerequisites_assignment',
							'label'     => __( 'Assignments', 'coschool' ),
							'type'      => 'select',
							'multiple'  => true,
							'chosen'    => true,
							'options'	=> Helper::get_posts( $assignment_args ),
							// 'desc' 		=> __( 'Randomize questions on view time', 'coschool' ),
						],
					]
				],
				'coschool_quiz_config_time'	=> [
					'id'        => 'coschool_quiz_config_time',
					'label'     => __( 'Time', 'coschool' ),
					'icon'      => 'dashicons-clock',
					'no_heading'=> false,
					'fields'	=> [						
						'enable_quiz_time'	=> [
							'id'		=> 'enable_quiz_time',
							'label'     => __( 'Enable Quiz Timing', 'coschool' ),
							'type'      => 'switch',
							'desc' 		=> __( 'Enable quiz duration', 'coschool' ),
						],
						'quiz_time' => [
							'id'        => 'quiz_time',
							'label'     => __( 'Time', 'coschool' ),
							'type'      => 'group',
							'desc'      => __( 'Set the duration of current quiz', 'coschool' ),
							'items'   	=> [
								'quiz_time_h' 	=> [
									'id'		=> 'quiz_time_h',
									'label'     => __( 'Hour', 'coschool' ),
									'type'      => 'number',
									'placeholder'   => __( 'Hour', 'coschool' ),
								],
								'quiz_time_m' 	=> [
									'id'		=> 'quiz_time_m',
									'label'     => __( 'Minute', 'coschool' ),
									'type'      => 'number',
									'placeholder'   => __( 'Minute', 'coschool' ),
								],
								'quiz_time_s' 	=> [
									'id'		=> 'quiz_time_s',
									'label'     => __( 'Seconds', 'coschool' ),
									'type'      => 'number',
									'placeholder'   => __( 'Second', 'coschool' ),
								],
							],
							'condition'		=> [
								'key'		=> 'enable_quiz_time',
								'compare'	=> 'checked'
							]
						],
					]
				],
				'coschool_quiz_config_retake'	=> [
					'id'        => 'coschool_quiz_config_retake',
					'label'     => __( 'Retake', 'coschool' ),
					'icon'      => 'dashicons-image-rotate',
					'no_heading'=> false,
					'fields'	=> [

						'enable_retake'	=> [
							'id'		=> 'enable_retake',
							'label'     => __( 'Enable Retake', 'coschool' ),
							'type'      => 'switch',
							'desc' 		=> __( 'Allow student to give quiz again', 'coschool' ),
						],
						'quiz_retake_time' => [
							'id'        => 'quiz_retake_time',
							'label'     => __( 'Retake Time', 'coschool' ),
							'type'      => 'number',
							'desc'      => __( 'How many time a student can give retake', 'coschool' ),
							'condition'	=> [
								'key'		=> 'enable_retake',
								'compare'	=> 'checked'
							]
						],
						'quiz_retake_delay' => [
							'id'        => 'quiz_retake_delay',
							'label'     => __( 'Retake Delay', 'coschool' ),
							'type'      => 'group',
							'desc'      => __( 'Delay between two retakes', 'coschool' ),
							'items'   	=> [
								'retake_delay_d' => [
									'id'		=> 'retake_delay_d',
									'label'     => __( 'Day', 'coschool' ),
									'type'      => 'number',
									'placeholder'   => __( 'Day', 'coschool' ),
								],
								'retake_delay_h' => [
									'id'		=> 'retake_delay_h',
									'label'     => __( 'Hour', 'coschool' ),
									'type'      => 'number',
									'placeholder'   => __( 'Hour', 'coschool' ),
								],
								'retake_delay_m' => [
									'id'		=> 'retake_delay_m',
									'label'     => __( 'Minute', 'coschool' ),
									'type'      => 'number',
									'placeholder'   => __( 'Minute', 'coschool' ),
								],
								'retake_delay_s' => [
									'id'		=> 'retake_delay_s',
									'label'     => __( 'Seconds', 'coschool' ),
									'type'      => 'number',
									'placeholder'   => __( 'Second', 'coschool' ),
								],
							],
							'condition'		=> [
								'key'		=> 'enable_retake',
								'compare'	=> 'checked'
							]
						],
					]
				],
				'coschool_quiz_config_deadline'	=> [
					'id'        => 'coschool_quiz_config_deadline',
					'label'     => __( 'Deadline', 'coschool' ),
					'icon'      => 'dashicons-calendar-alt',
					'no_heading'=> false,
					'fields'	=> [						
						'enable_dead_line'	=> [
							'id'		=> 'enable_dead_line',
							'label'     => __( 'Enable Deadline', 'coschool' ),
							'type'      => 'switch',
						],
						'quiz_deadline' => [
							'id'        => 'quiz_deadline',
							'label'     => __( 'Deadline', 'coschool' ),
							'type'      => 'group',
							'desc'      => __( 'Set the Deadline of current quiz', 'coschool' ),
							'items'		=> [
								'quiz_deadline_d' 	=> [
									'id'			=> 'quiz_deadline_d',
									'label'     	=> __( 'Day', 'coschool' ),
									'type'      	=> 'date',
									'placeholder'   => __( 'Date', 'coschool' ),
								],
								'quiz_deadline_t' 	=> [
									'id'			=> 'quiz_deadline_t',
									'label'     	=> __( 'Time', 'coschool' ),
									'type'      	=> 'time',
									'placeholder'   => __( 'Time', 'coschool' ),
								],
							],
							'condition'		=> [
								'key'		=> 'enable_dead_line',
								'compare'	=> 'checked'
							]
						],
					]
				],
			]
		];

		$metabox = apply_filters( 'coschool_quiz_config_metabox', $metabox, $this );

		new Metabox_API( $metabox );
	}

	public function view_course( $section ){
		if ( !isset( $section['id'] ) || $section['id'] != 'coschool_quiz_config' || !isset( $_GET['post'] ) ) return;

		$quiz_data		= new Data( sanitize_text_field( $_GET['post'] ) );
		$quiz_course	= $quiz_data->get( 'course_id' );

		?>
		<div class="cx-row">
			<div class="cx-label-wrap">
				<label for="coschool_quiz_config-enable_quiz_time"><?php _e( 'Course', 'coschool' ) ?></label>
			</div>
			<div class="cx-field-wrap">
			<?php
			if ( $quiz_course ) {
				printf( '<a href="%s">%s</a>', get_edit_post_link( $quiz_course ), get_the_title( $quiz_course ) ); 
			}
			else{
				_e( 'Not Assigned', 'coschool' );
			}
			?>
			</div>
		</div>
		<input type="hidden" name="coschool_quiz_prerequisites['course_id']" value="<?php esc_attr_e( $quiz_course ) ?>">
		<?php		
	}

	public function save( $quiz_id, $quiz ){
		if ( isset( $_POST['questions'] ) && count(  $_POST['questions'] ) > 1 ) {
			$questions = get_post_meta( $quiz_id, 'questions', true );
			$questions = $questions ? $questions : [];

			$_questions = $_POST['questions'];

			if ( isset( $_questions['%%ques_set%%'] ) ) unset( $_questions['%%ques_set%%'] );

			$new_questions = [];
			foreach ( $_questions as $question ) {

				$_question = [
					'post_title' 	=> coschool_sanitize( $question['title'] ),
					'post_type'		=> 'question',
					'post_status'	=> 'publish',
					'meta_input'	=> [
						'question_type' => isset( $question['type'] ) ? coschool_sanitize( $question['type'] ) : '',
						'question_mark' => isset( $question['mark'] ) ? coschool_sanitize( $question['mark'] ) : 0,
						'question_required' => isset( $question['is_required'] ) ? 'yes' : 'no',
						'quiz_id' 		=> $quiz_id,
					],
				];

				if ( isset( $question['type'] ) && in_array( $question['type'], [ 'mcq', 'true_false' ] ) ) {
					$_question['meta_input']['question_options'] = isset( $question['options'] ) && !empty( $question['options'] ) ? array_map( 'coschool_sanitize', $question['options'] ) : [];

					$_question['meta_input']['question_correct'] = isset( $question['correct'] ) && !empty( $question['correct'] ) ? array_map( 'coschool_sanitize', $question['correct'] ) : [];
				}

				if ( isset( $question['id'] ) && in_array( $question['id'], $questions ) ) {
					$new_questions[] = $question['id'];
					$_question['ID'] = $question['id'];
					wp_update_post( $_question );
				}
				else {
					$id = wp_insert_post( $_question );
					$new_questions[] = $id;
				}
			}

			$removed_questions = array_diff( $questions, $new_questions );

			if ( !empty( $removed_questions ) ) {
				foreach ( $removed_questions as $question_id ) {
					wp_delete_post( coschool_sanitize( $question_id ), true );
				}
			}
			update_post_meta( $quiz_id, 'questions', array_unique( $new_questions ) );
		}
	}
}