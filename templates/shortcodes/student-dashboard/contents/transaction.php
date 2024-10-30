<?php 
use Codexpert\Plugin\Table;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
$student_data 		= new Student_Data( get_current_user_id() );
$payments 			= $student_data->get_payments();
$format 			= get_option( 'date_format' );
$url 				= coschool_dashoard_endpoint( 'courses' );

$transaction_url 	= coschool_dashoard_endpoint( 'transaction' );

$payment_data = [];
foreach ( $payments as $payment ):
	$id 			= $payment->id;
	$amount 		= coschool_price( $payment->amount );
	$student 		= $payment->student;
	$method 		= $payment->method;
	$transaction_id = $payment->transaction_id;
	$reference 		= $payment->reference;
	$date 			= date( $format, $payment->time );

	$payment_data[] =  [ 'date' => $date, 'amount' => $amount, 'method' => $method, 'transaction_id' => $transaction_id, 'reference' => $reference ];
endforeach;
?>

<div class="coschool-dashboard-header">
	<h2 class="coschool-dashboard-title"><?php _e( 'Transaction', 'coschool' ); ?></h2>
</div>
<div class="coschool-dashboard-body transaction-panel">
	<?php

		$config = [
		'per_page'			=> 20,
		'columns'			=> [
			'date'			=> __( 'Date', 'coschool' ),
			'amount'		=> __( 'Amount', 'coschool' ),
			'method'		=> __( 'Payment method', 'coschool' ),
			'transaction_id'=> __( 'Transaction id', 'coschool' ),
			'reference'		=> __( 'Reference', 'coschool' ),
		],
		'sortable'		=> [ 'date', 'amount' ],
		'orderby'		=> 'date',
		'order'			=> 'desc',
		'data'			=> $payment_data,
	];

	$table = new Table( $config );
	echo '<form method="post">';
	$table->prepare_items();
	$table->display();
	echo '</form>';
	?>
</div>
