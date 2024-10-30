<?php
use Codexpert\Plugin\Table;
use Codexpert\CoSchool\App\Quiz\Attempt;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\Helper;

if( ! isset( $_GET['quiz'] ) || '' == ( $quiz_id = coschool_sanitize( $_GET['quiz'] ) ) ) {
	wp_redirect( admin_url( 'edit.php?post_type=quiz' ) );
}

?>
<div class="wrap">
	<h2>
		<?php
		// Translators: %s is the quiz title.
		printf( __( 'Quiz: %s', 'coschool' ), get_the_title( $quiz_id ) );
		?>
	</h2>
	<?php
	$db = new DB;
	$quiz_attempts = $db->select( 'quiz_attempts', '*', "`quiz_id` = {$quiz_id}" );
	$admin_url = admin_url( 'admin.php' );

	$config = [
		'per_page'		=> 50,
		'columns'		=> [
			'id'			=> __( 'Attempt ID', 'coschool' ),
			'quiz_id'		=> __( 'Quiz ID', 'coschool' ),
			'student'		=> __( 'Student Name', 'coschool' ),
			'time_taken'	=> __( 'Time Taken', 'coschool' ),
			'time'			=> __( 'Time', 'coschool' ),
			'actions'		=> __( '', 'coschool' ),
		],
		'sortable'		=> [ 'id', 'student', 'time_taken' ],
		'orderby'		=> 'id',
		'order'			=> 'desc',
		'data'			=> [],
		'bulk_actions'	=> [],
	];

	$time_format = get_option( 'links_updated_date_format' );
	foreach ( $quiz_attempts as $attempt ) {

		$time_taken = round( $attempt->time_taken );
		$time_taken = sprintf('%02dh : %02dm : %02ds', ($time_taken/ 3600),($time_taken/ 60 % 60), $time_taken% 60);

		$config['data'][] 	= [
			'id'			=> $attempt->id,
			'quiz_id'		=> $attempt->quiz_id,
			'student'		=> ( new Student_Data( $attempt->student ) )->get( 'name' ),
			'time_taken' 	=> $time_taken,
			'time'			=> wp_date( $time_format, $attempt->time ),
			'actions'		=> "<a href='" . add_query_arg( [ 'page' => 'quiz-attempts', 'attempt' => $attempt->id ], $admin_url ) . "'>" . __( 'Review', 'coschool' ) . "</a>",
		];
	}

	$table = new Table( $config );
	echo '<form method="post">';
	$table->prepare_items();
	$table->display();
	echo '</form>';
	?>
</div>