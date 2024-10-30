<?php
/**
 * All meta facing functions
 */
namespace Codexpert\CoSchool\App\Assignment;
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
 * @subpackage Meta
 * @author Codexpert <hi@codexpert.io>
 */
class Meta {

	/**
	 * Constructor function
	 */
	public function __construct() {}    

    /**
     * Generates config metabox
     * 
     * @uses add_meta_box()
     */
    public function config() {
        $lesson_args        = [ 'post_type' => 'lesson' ];
        $quiz_args          = [ 'post_type' => 'quiz'   ];
        $assignment_args    = [ 'post_type' => 'assignment' ];
        $current_user_id    = get_current_user_id();

        if( ! current_user_can( 'edit_pages' ) ) {
            $lesson_args['author']      = $current_user_id;
            $quiz_args['author']        = $current_user_id;
            $assignment_args['author']  = $current_user_id;
        }

        $metabox = [
            'id'            => 'coschool-assignment-settings',
            'label'         => __( 'Assignment Configuration', 'coschool' ),
            'post_type'     => 'assignment',
            'topnav'        => wp_is_mobile(),
            'sections'      => [
                'coschool_assignment_config'    => [
                    'id'        => 'coschool_assignment_config',
                    'label'     => __( 'General', 'coschool' ),
                    'icon'      => 'dashicons-admin-tools',
                    'no_heading'=> true,
                    'fields'    => [
                        'prerequisites_heading' => [
                            'id'        => 'prerequisites_heading',
                            'label'     => __( 'Prerequisites', 'coschool' ),
                            'type'      => 'divider',
                        ],
                        'prerequisites_lesson'  => [
                            'id'        => 'prerequisites_lesson',
                            'label'     => __( 'Lessons', 'coschool' ),
                            'type'      => 'select',
                            'multiple'  => true,
                            'chosen'      => true,
                            'options'   => Helper::get_posts( $lesson_args ),
                        ],
                        'prerequisites_quiz'    => [
                            'id'        => 'prerequisites_quiz',
                            'label'     => __( 'Quizzes', 'coschool' ),
                            'type'      => 'select',
                            'multiple'  => true,
                            'chosen'      => true,
                            'options'   => Helper::get_posts( $quiz_args ),
                        ],
                        'prerequisites_assignment'  => [
                            'id'        => 'prerequisites_assignment',
                            'label'     => __( 'Assignments', 'coschool' ),
                            'type'      => 'select',
                            'multiple'  => true,
                            'chosen'      => true,
                            'options'   => Helper::get_posts( $assignment_args ),
                        ],
                    ]
                ],
            ]
        ];

        $metabox = apply_filters( 'coschool_assignment_config_metabox', $metabox, $this );

        new Metabox_API( $metabox );
    }
    
    public function view_course( $section ){
        if ( !isset( $section['id'] ) || $section['id'] != 'coschool_assignment_config' || !isset( $_GET['post'] ) ) return;

        $assignment_data    = new Data( sanitize_text_field( $_GET['post'] ) );
        $assignment_course  = $assignment_data->get( 'course_id' );
        ?>
        <div class="cx-row">
            <div class="cx-label-wrap">
                <label for="coschool_assignment_config-enable_assignment_time"><?php _e( 'Course', 'coschool' ) ?></label>
            </div>
            <div class="cx-field-wrap ">
            <?php 
            if ( $assignment_course ) {
                printf( '<a href="%s">%s</a>', get_edit_post_link( $assignment_course ), get_the_title( $assignment_course ) ); 
            }
            else{
                _e( 'Not Assigned', 'coschool' );
            }
            ?>
            </div>
        </div>
        <input type="hidden" name="coschool_assignment_config[course_id]" value="<?php echo $assignment_course; ?>">
        <?php       
    }
}