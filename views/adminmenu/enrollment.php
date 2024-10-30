<?php
use Codexpert\Plugin\Table;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
?>

<div class="wrap">
	<h2><?php _e( 'Enrollments', 'coschool' ); ?></h2>
	<?php
	global $wpdb;
	$coschool_prefix = coschool_db_prefix();

	$sql 		= "SELECT * FROM `{$wpdb->prefix}{$coschool_prefix}enrollments`";

	$payment_id = isset( $_GET['payment_id'] ) ? coschool_sanitize( $_GET['payment_id'] ) : '';
	
	if ( ! empty( $payment_id ) ) {
		$sql .= " WHERE `payment_id` = {$payment_id}";
	}

	$enrollments = $wpdb->get_results( $sql );

	$config = [
		'per_page'			=> 50,
		'columns'			=> [
			'id'			=> __( 'ID', 'coschool' ),
			'course_id'		=> __( 'Course', 'coschool' ),
			'student'		=> __( 'Student', 'coschool' ),
			'price'			=> __( 'Amount', 'coschool' ),
			'payment_id'	=> __( 'Payment ID', 'coschool' ),
			'time'			=> __( 'Time', 'coschool' ),
			'status'		=> __( 'Status', 'coschool' ),
			'action'		=> __( 'Action', 'coschool' ),
		],
		'sortable'			=> [ 'id', 'course_id', 'student', 'price', 'payment_id', 'status' ],
		'orderby'			=> 'id',
		'order'				=> 'desc',
		'data'				=> [],
		'bulk_actions'		=> [],
	];

	$time_format 	= get_option( 'links_updated_date_format' );

	foreach ( $enrollments as $enrollment ) {
		$course_data 		= new Course_Data( $enrollment->course_id );
		$student_data 		= new Student_Data( $enrollment->student );
		$status     		= $enrollment->status;

		$payment_url = add_query_arg( array( 
		    'payment_id' 	=> $enrollment->payment_id,
		), admin_url( 'admin.php?page=payments' ) );

		$action_btns 	= [
			'active' 	=> "<button class='coschool-enrollment-action' data-enrollment='" . esc_attr( $enrollment->id ) . "' data-action='active'>" . __( 'Approve', 'coschool' ) . "</button>",
			'pending' 	=> "<button class='coschool-enrollment-action' data-enrollment='" . esc_attr( $enrollment->id ) . "' data-action='pending'>" . __( 'Decline', 'coschool' ) . "</button>",
			'blocked' 	=> "<button class='coschool-enrollment-action' data-enrollment='" . esc_attr( $enrollment->id ) . "' data-action='blocked'>" . __( 'Block', 'coschool' ) . "</button>",
		];

		if ( $status && isset( $action_btns[ $status ] ) ) unset( $action_btns[ $status ] );
		if ( $status && $status != 'pending' ) unset( $action_btns[ 'pending' ] );
		
		$config['data'][] 	= [
			'id'			=> $enrollment->id,
			'course_id'		=> $course_data->get( 'name' ),
			'student'		=> $student_data->get( 'name' ),
			'price'			=> coschool_price( $enrollment->price ),
			'payment_id'	=> $enrollment->payment_id == 0 ? '-' : "#<a href='". esc_url( $payment_url ) ."'>'". esc_html( $enrollment->payment_id ) ."'</a>",
			'time'			=> date_i18n( $time_format, $enrollment->time ),
			'status'		=> '<button type="button" class="coschool-status coschool-status-' . esc_attr( $enrollment->status ) . '">' . ucfirst( $enrollment->status ) . '</button>',
			'action'		=> implode( '', $action_btns ),
		];
	}

	$table = new Table( $config );
	echo '<form method="post">';
	$table->prepare_items();
	$table->search_box( 'Search', 'search' );
	$table->display();
	echo '</form>';
	?>
</div>