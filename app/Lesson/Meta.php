<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Lesson;
use Codexpert\CoSchool\Helper;
use Codexpert\Plugin\Metabox as Metabox_API;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Lesson
 * @author Codexpert <hi@codexpert.io>
 */
class Meta {

	/**
	 * Generates config metabox
	 * 
	 * @uses add_meta_box()
	 */
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
			'id'			=> 'coschool-lesson-settings',
			'label'			=> __( 'Lesson Configuration', 'coschool' ),
			'post_type'		=> 'lesson',
			'topnav'		=> wp_is_mobile(),
			'sections'		=> [
				'coschool_lesson_config'	=> [
					'id'        => 'coschool_lesson_config',
					'label'     => __( 'General', 'coschool' ),
					'icon'      => 'dashicons-admin-tools',
					'no_heading'=> true,
					'fields'    => [						
						'free_lesson'	=> [
							'id'		=> 'free_lesson',
							'label'     => __( 'Is Free?', 'coschool' ),
							'type'      => 'switch',
							'default'	=> 'off',
							'desc' 		=> __( 'Check this if you want to keep this lesson open for all even if it belongs to a paid course.', 'coschool' ),
						],
						'prerequisites_heading'	=> [
							'id'		=> 'prerequisites_heading',
							'label'     => __( 'Prerequisites', 'coschool' ),
							'type'      => 'divider',
						],
						'prerequisites_lesson'	=> [
							'id'		=> 'prerequisites_lesson',
							'label'     => __( 'Lessons', 'coschool' ),
							'type'      => 'select',
							'multiple'  => true,
							'chosen'    => true,
							'options'	=> Helper::get_posts( $lesson_args ),
						],
						'prerequisites_quiz'	=> [
							'id'		=> 'prerequisites_quiz',
							'label'     => __( 'Quizzes', 'coschool' ),
							'type'      => 'select',
							'multiple'  => true,
							'chosen'    => true,
							'options'	=> Helper::get_posts( $quiz_args ),
						],
						'prerequisites_assignment'	=> [
							'id'		=> 'prerequisites_assignment',
							'label'     => __( 'Assignments', 'coschool' ),
							'type'      => 'select',
							'multiple'  => true,
							'chosen'    => true,
							'options'	=> Helper::get_posts( $assignment_args ),
						],
					]
				],
			]
		];

		$metabox = apply_filters( 'coschool_lesson_config_metabox', $metabox, $this );

		new Metabox_API( $metabox );
	}
	
	public function view_course( $section ){
		if ( !isset( $section['id'] ) || $section['id'] != 'coschool_lesson_config' || !isset( $_GET['post'] ) ) return;

		$lesson_data	= new Data( sanitize_text_field( $_GET['post'] ) );
		$lesson_course	= $lesson_data->get( 'course_id' );

		?>
		<div class="cx-row">
			<div class="cx-label-wrap">
				<label for="coschool_lesson_config-enable_lesson_time"><?php _e( 'Course', 'coschool' ) ?></label>
			</div>
			<div class="cx-field-wrap ">
			<?php
			if ( $lesson_course ) {
				printf( '<a href="%s">%s</a>', get_edit_post_link( $lesson_course ), get_the_title( $lesson_course ) ); 
			}
			else{
				esc_html_e( 'Not Assigned', 'coschool' );
			}
			?>
			</div>
		</div>
		<input type="hidden" name="coschool_lesson_config[course_id]" value="<?php echo esc_attr( $lesson_course ); ?>">
		<?php		
	}
}