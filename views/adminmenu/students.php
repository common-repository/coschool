<?php
use Codexpert\Plugin\Table;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;

$args = [ 'capability__in' => 'read_courses' ];

// Filter by course
if( isset( $_GET['course_id'] ) && 'course' == get_post_type( $course_id = coschool_sanitize( $_GET['course_id'] ) ) ) {
	$course_data	= new Course_Data( $course_id );

	$students = [];
	foreach ( $course_data->get( 'students' ) as $item ) {
		$args['include'][] = $item->student;
	}
}

$students = get_users( $args );

$config = [
	'per_page'		=> 50,
	'columns'		=> [
		'name'		=> __( 'Name', 'coschool' ),
		'email'		=> __( 'Email', 'coschool' ),
		'courses'	=> __( 'Courses', 'coschool' ),
		'spent'		=> __( 'Total Spent', 'coschool' ),
		'joined'	=> __( 'Joined', 'coschool' ),
	],
	'sortable'		=> [ 'id', 'name', 'spent' ],
	'orderby'		=> 'joined',
	'order'			=> 'desc',
	'data'			=> [],
	'bulk_actions'	=> [],
];

$time_format = get_option( 'links_updated_date_format' );
foreach ( $students as $id => $_student ) {
	$student = new Student_Data( $_student->ID );
	$config['data'][] = [
		'name'		=> $student->get( 'name' ) . ' (ID: ' . $student->get( 'id' ) . ')',
		'email'		=> $student->get( 'email' ),
		'courses'	=> sprintf( __( '%d Courses', 'coschool' ), count( $student->get_courses() ) ),
		'spent'		=> coschool_price( $student->get( 'spent' ), false ),
		'joined'	=> wp_date( $time_format, strtotime( $student->get( 'joined' ) ) ),
	];
}
?>

<div class="wrap">
	<h2><?php _e( 'Students', 'coschool' ); ?></h2>
	<?php
	$table = new Table( $config );
	echo '<form method="post">';
	$table->prepare_items();
	$table->search_box( 'Search', 'search' );
	$table->display();
	echo '</form>';
	?>
</div>