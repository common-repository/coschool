<?php
use Codexpert\CoSchool\App\Quiz\Data;

global $post;

$quiz_data		= new Data( $post->ID );
$quiz_course	= $quiz_data->get( 'course_id' );
wp_enqueue_style( 'jquery-ui-datepicker' );
wp_enqueue_script( 'jquery-ui-datepicker' );

$metabox = [
	'id'			=> 'coschool-course-settings',
	'label'			=> __( 'Course Configuration', 'coschool' ),
	'post_type'		=> 'course',
	'topnav'		=> wp_is_mobile(),
	'sections'		=> [
		'coschool_pricing'	=> [
			'id'        => 'coschool_pricing',
			'label'     => __( 'Pricing', 'coschool' ),
			'icon'      => 'dashicons-admin-tools',
			'no_heading'=> true,
			'fields'    => [
				'price_type' => [
					'id'        => 'price_type',
					'label'     => __( 'Course Type', 'coschool' ),
					'type'      => 'select',
					'desc'      => __( 'Set the type of course', 'coschool' ),
					'options'   => [
						'free'		=> __( 'Free', 'coschool' ),
						'premium'	=> __( 'Premium', 'coschool' ),
					],
					'default'	=> 'premium',
				],
				'price' 	=> [
					'id'        => 'price',
					'label'     => __( 'Price', 'coschool' ),
					'type'      => 'number',
					'min'		=> 0,
					'step'		=> 0.01,
					'desc'      => __( 'Input course price without the currency symbol.', 'coschool' ),
					'condition'	=> [
						'key'	=> 'price_type',
						'value'	=> 'premium'
					]
				],
			]
		],
		'coschool_certification'	=> [
			'id'        => 'coschool_certification',
			'label'     => __( 'Certification', 'coschool' ),
			'icon'      => 'dashicons-text-page',
			'no_heading'=> true,
			'fields'    => [
				'enable_certificate' => [
					'id'      	=> 'enable_certificate',
					'label'     => __( 'Enable', 'coschool' ),
					'type'      => 'select',
					'desc'      => __( 'Are you going to issue a certificate after completing this course?', 'coschool' ),
					'options'   => [
						'yes'	=> __( 'Yes', 'coschool' ),
						'no'	=> __( 'No', 'coschool' ),
					],
					'default'   => 'no',
				],
				'certificate' 	=> [
					'id'        => 'certificate',
					'label'     => __( 'Choose a Certificate', 'coschool' ),
					'type'      => 'select',
					'options'	=> [],
					'condition'	=> [
						'key'	=> 'enable_certificate',
						'value'	=> 'yes'
					]
				],
			]
		],
	]
];

new Metabox_API( $metabox );
?>

<div>
	<label for="quiz-course"><?php _e( 'Course: ', 'coschool' ); ?></label>
	<?php
	if( $quiz_course != '' ) {
		printf( '<a href="%s">%s</a>', get_edit_post_link( $quiz_course ), get_the_title( $quiz_course ) );
	}
	else {
		_e( '[Not assigned]', 'coschool' );
	}
	?>
</div>
<div id="quiz-configuration">
	<div id="quiz-time-container" class="quiz-congig-inputs">
		<label class="label" for="quiz-time"><?php _e( 'Time', 'coschool' ); ?></label>
		<input id="quiz-time" type="number" name="quiz_time" class="coschool-time">
	</div>
	<div id="quiz-randomize-container" class="quiz-congig-inputs">
		<label class="label" for="quiz-randomize"><?php _e( 'Randomize', 'coschool' ); ?></label>
		<label class="quiz-switch">
			  <input id="quiz-randomize" type="checkbox" name="quiz_randomize">
			  <span class="slider round"></span>
		</label>
	</div>
	<div id="quiz-view-container" class="quiz-congig-inputs">
		<label class="label" for="quiz-view"><?php _e( 'Question at once', 'coschool' ); ?></label>
		<label class="quiz-switch">
			  <input id="quiz-view" type="checkbox" name="quiz_view">
			  <span class="slider round"></span>
		</label>
	</div>
	<div id="quiz-retake-container" class="quiz-congig-inputs">
		<div class="quiz-retake-enable">
			<label class="label" for="quiz-retake"><?php _e( 'Retake Option', 'coschool' ); ?></label>
			<label class="quiz-switch">
				  <input id="quiz-retake" type="checkbox" name="quiz_retake">
				  <span class="slider round"></span>
			</label>
		</div>
		<div class="quiz-retake-count-container" style="display:none;">
			<label class="label" for="quiz-retake-count"><?php _e( 'Available Retakes', 'coschool' ); ?></label>
			<input id="quiz-retake-count" type="number" name="quiz_retake-count" class="coschool-retake-count">
		</div>
		<div class="quiz-retake-delay-container" style="display:none;">
			<label class="label" for="quiz-retake-delay"><?php _e( 'Retake Delay', 'coschool' ); ?></label>
			<input id="quiz-retake-delay" type="number" name="quiz_retake-delay" class="coschool-retake-delay">
		</div>
	</div>
	<div id="quiz-deadline-container" class="quiz-congig-inputs">
		<div class="quiz-deadline-enable">
			<label class="label" for="quiz-deadline-enabling"><?php _e( 'Deadline Option', 'coschool' ); ?></label>
			<label class="quiz-switch">
				  <input id="quiz-deadline-enabling" type="checkbox" name="quiz_deadline_enabled">
				  <span class="slider round"></span>
			</label>
		</div>
		<div class="quiz-deadline-date-container">
			<label class="label" for="quiz-deadline"><?php _e( 'Deadline', 'coschool' ); ?></label>
			<input id="quiz-deadline" type="text" name="quiz_deadline" class="coschool-deadline">
		</div>
	</div>
</div>