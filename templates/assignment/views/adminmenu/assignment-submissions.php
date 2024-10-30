<?php
use Codexpert\Plugin\Table;
use Codexpert\CoSchool\App\Assignment\Data as Assignment_data;;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\Helper;

if( ! isset( $_GET['assignment'] ) || '' == ( $assignment_id = coschool_sanitize( $_GET['assignment'] ) ) ) {
	wp_redirect( admin_url( 'edit.php?post_type=assignment' ) );
}

?>
<div class="wrap">
	<h2><?php printf( __( 'Assignment: <a href="%1$s">%2$s</a>', 'coschool' ), get_edit_post_link( $assignment_id ), get_the_title( $assignment_id ) ); ?></h2>
	<?php
	$assignment_data = new Assignment_data( $assignment_id );
	$submissions = $assignment_data->get_submissions();
	$admin_url = admin_url( 'admin.php' );

	$config = [
		'per_page'		=> 50,
		'columns'		=> [
			'id'				=> __( 'Submission ID', 'coschool' ),
			'student'			=> __( 'Student Name', 'coschool' ),
			'attachments'		=> __( 'Attachments', 'coschool' ),
			'time'				=> __( 'Submitted', 'coschool' ),
			'actions'			=> __( '', 'coschool' ),
		],
		'sortable'		=> [ 'id', 'student', 'time_taken' ],
		'orderby'		=> 'id',
		'order'			=> 'desc',
		'data'			=> [],
		'bulk_actions'	=> [],
	];

	$time_format = get_option( 'links_updated_date_format' );
	foreach ( $submissions as $submission ) {

		$attachments = [];
		if( ! is_null( $_attachments = coschool_unserialize( $submission->reference ) ) && count( $_attachments ) > 0 ) {
			foreach ( $_attachments as $attachment ) {
				$attachments[] = sprintf( '<a href="%s" download>%s</a>', wp_get_attachment_url( $attachment ), basename ( get_attached_file( $attachment ) ) );
			}
		}

		$attachments 	= '<p>' . implode( '</p><p>', $attachments ) . '</p>';
		$action_input  	= '<button class="coschool-review-btn" data-type="right"><span class="dashicons dashicons-yes"></span></button>
									<button class="coschool-review-btn" data-type="wrong"><span class="dashicons dashicons-no-alt"></span></button>';

		$config['data'][] = [
			'id'				=> $submission->id,
			'student'			=> ( new Student_Data( coschool_get_enrollment( $submission->enrollment_id )->student ) )->get( 'name' ),
			'attachments'		=> $attachments,
			'time'				=> wp_date( $time_format, $submission->completed_at ),
			'actions'			=> $action_input,
		];
	}

	$table = new Table( $config );
	echo '<form method="post">';
	$table->prepare_items();
	$table->display();
	echo '</form>';
	?>
</div>