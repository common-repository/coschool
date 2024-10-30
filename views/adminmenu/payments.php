<?php
use Codexpert\Plugin\Table;
use Codexpert\CoSchool\App\Payment\Data;
use Codexpert\CoSchool\App\Student\Data as Student;
?>

<div class="wrap">
	<h2><?php _e( 'Payments', 'coschool' ); ?></h2>
	<?php
	global $wpdb;
	$db_prefix 	= coschool_db_prefix();

	$sql 		= "SELECT * FROM `{$wpdb->prefix}{$db_prefix}payments`";

	$payment_id = isset( $_GET['payment_id'] ) ? coschool_sanitize( $_GET['payment_id'] ) : '';

	if ( ! empty( $payment_id ) ) {
		$sql .= " WHERE `id` = {$payment_id}";
	}

	$payments 	= $wpdb->get_results( $sql );

	$config 	= [
		'per_page'		=> 50,
		'columns'		=> [
			'id'				=> __( 'ID', 'coschool' ),
			'amount'			=> __( 'Amount', 'coschool' ),
			'student'			=> __( 'Student', 'coschool' ),
			'method'			=> __( 'Method', 'coschool' ),
			'transaction_id'	=> __( 'Transaction ID', 'coschool' ),
			'reference'			=> __( 'Reference', 'coschool' ),
			'time'				=> __( 'Time', 'coschool' ),
		],
		'sortable'		=> [ 'id', 'student', 'method', 'reference' ],
		'orderby'		=> 'id',
		'order'			=> 'desc',
		'data'			=> [],
		'bulk_actions'	=> [],
	];

	$time_format = get_option( 'links_updated_date_format' );

	foreach ( $payments as $id => $payment ) {

		if( array_key_exists( $payment->method, coschool_payment_providers() ) ) {
			$payment_method = ucwords( sprintf( __( 'Native - %s', 'coschool' ), $payment->method ) );
		}
		else {
			$payment_method = ucwords( str_replace( '-', ' ', $payment->method ) );
		}

		$enrollment_url 	= add_query_arg( array( 
		    'payment_id' 	=> $payment->id,
		), admin_url( 'admin.php?page=enrollment' ) );
		
		$config['data'][] = [
			'id'				=> '<a href="'. $enrollment_url .'">'. $payment->id .'</a>',
			'amount'			=> coschool_price( $payment->amount ),
			'student'			=> ( new Student( $payment->student ) )->get( 'name' ),
			'method'			=> $payment_method,
			'transaction_id'	=> $payment->transaction_id,
			'reference'			=> "<a href='" . get_edit_post_link( $payment->reference ) . "'>'". esc_html( $payment->reference ) ."'</a>",
			'time'				=> wp_date( $time_format, $payment->time ),
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