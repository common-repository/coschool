<?php
use Codexpert\Plugin\Table;
use Codexpert\CoSchool\App\Quiz\Attempt;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Question\Data as Question_Data;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\Helper;

if( ! isset( $_GET['attempt'] ) || '' == ( $attempt = coschool_sanitize( $_GET['attempt'] ) ) ) {
	wp_redirect( admin_url( 'edit.php?post_type=quiz' ) );
}

wp_enqueue_style( 'coschool-admin-quiz' );
wp_enqueue_script( 'coschool-admin-quiz' );
?>
<div class="wrap">
	<h2><?php printf( __( 'Quiz Attempts: #%d', 'coschool' ), coschool_sanitize( $attempt ) ); ?></h2>
	<?php
	$db = new DB;
	$quiz_attempt_answers = $db->select( 'quiz_attempt_answers', '*', "`attempt_id` = ". coschool_sanitize( $attempt ) ."" );
	$admin_url = admin_url( 'admin.php' );

	$config = [
		'per_page'		=> 50,
		'columns'		=> [
			'id'		=> __( 'Attempt ID', 'coschool' ),
			'question'	=> __( 'Question', 'coschool' ),
			'correct'	=> __( 'Correct Answer', 'coschool' ),
			'answer'	=> __( 'Answer', 'coschool' ),
			'points'	=> __( 'Points', 'coschool' ),
			'feedback'	=> __( 'Feedback', 'coschool' ),
			'actions'	=> __( '', 'coschool' ),
		],
		'sortable'		=> [ 'id', 'student', 'time_taken' ],
		'orderby'		=> 'id',
		'order'			=> 'desc',
		'data'			=> [],
		'bulk_actions'	=> [],
	];

	$time_format = get_option( 'links_updated_date_format' );
	foreach ( $quiz_attempt_answers as $_answer ) {

		$question_data 	= new Question_Data( $_answer->question );
		$point 			= $_answer->points;
		$point_input 	= '<input class="coschool-attempt-point" type="number" min="0" max="' . esc_attr( $point ) . '" value="' . esc_attr( $_answer->points ) . '" />';
		$point_input 	= ! in_array( $question_data->get_type(), [ 'true_false', 'mcq' ] ) ? $point_input : $point;
		$action_input  	= '<button class="coschool-review-btn" data-type="right"><span class="dashicons dashicons-yes"></span></button>
						<button class="coschool-review-btn" data-type="wrong"><span class="dashicons dashicons-no-alt"></span></button>';
		$action_input  	= ! in_array( $question_data->get_type(), [ 'true_false', 'mcq' ] ) ? $action_input : '';
		
		/**
		 * @todo 
		 */

		$config['data'][] = [
			'id'				=> $_answer->id,
			'question'			=> $question_data->get( 'name' ),
			'correct'			=> coschool_unserialize( $question_data->get( 'answer' ), ', ' ),
			'answer'			=> coschool_unserialize( $_answer->answer, ', ' ),
			'points'			=> $point_input,
			'feedback'			=> '<textarea class="coschool-attempt-review" placeholder="' . __( 'Add your feedback', 'coschool' ) . '">' . esc_attr( $_answer->feedback ) . '</textarea>',
			'actions'			=> $action_input,
		];
	}

	$table = new Table( $config );
	echo '<form id="coschool-attept-review-form" method="post">';
	$table->prepare_items();
	$table->display();
	echo '</form>';
	?>
	<div id="coschool-attempt-notification"><?php _e( 'Point Updated', 'coschool' ); ?></div>
</div>