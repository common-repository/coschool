<?php 
use Codexpert\CoSchool\App\Student\Data as Student_Data;

$student_data 	= new Student_Data( get_current_user_id() );
$wishlists 		= $student_data->get_wishlist();

$wishlist_url 	= coschool_dashoard_endpoint( 'wishlist' );
$paginated 		= isset( $_GET['paginated'] ) ? coschool_sanitize( $_GET['paginated'] ) : 1;
$limit 			= 6;
$offset 		= $limit * ( $paginated - 1 );
$total 			= $page_count = count( $wishlists );
$paginat_number = ceil( $total / $limit );
$wishlists 		= array_slice( $wishlists, $offset, $limit );
?>

<div class="coschool-dashboard-header">
	<h1 class="coschool-dashboard-title"><?php _e( 'Wishlist', 'coschool' ); ?></h1>
		<?php if ( count( $wishlists ) == 0 ): ?>
			<h6 class="coschool-dashboard-subtitle"><?php _e( 'You have no wishlist.', 'coschool' ); ?></h6>
		<?php else: ?>
			<h6 class="coschool-dashboard-subtitle"><?php _e( 'Your Account Wishlist.', 'coschool' ); ?></h6>
		<?php endif ?>
</div>
<div class="coschool-dashboard-body">
	<div class="coschool-dashboard-cards coschool-dashboard-wishlist">

			<?php foreach ( $wishlists as $course_id ): 
				$course_data		= new Codexpert\CoSchool\App\Course\Data( $course_id );
				$instructor_data	= new Codexpert\CoSchool\App\Instructor\Data( $course_data->get( 'author' ) );
				$student_data 		= new Codexpert\CoSchool\App\Student\Data( get_current_user_id() );

				$thumbnail 			= $course_data->get_thumbnail( 'coschool-thumb' );
				$link 				= get_the_permalink( $course_data->get( 'id' ) );
				$title 				= $course_data->get('title');
				?>

				<div id="course-<?php echo esc_attr( $course_data->get( 'id' ) ); ?>" class="course-card">
					<div class="course-header">
						<div class="course-thumbnail">
							<a href="<?php echo esc_url( $link ); ?>">
								<?php echo wp_kses_post( $thumbnail ); ?>
							</a>
						</div>
					</div>
					<div class="course-content">
						<h2 class="course-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h2>
						<div class="course-author">
							<div class="course-author-image">
									<?php echo '<img class="course-author-thumbnail" alt="' . esc_attr( $instructor_data->get( 'name' ) ) . '" src="' . esc_url( $instructor_data->get_avatar_url() ) . '">'; ?>
							</div>
							<div class="course-author-name">
								<?php printf( __( 'By <a href="%s">%s</a>', 'coschool' ), esc_url( $instructor_data->get( 'archive_url' ) ), esc_html( $instructor_data->get( 'name' ) ) ); ?>
							</div>
							<div class="course-category">
								<?php if ( $course_cats = get_the_term_list( $course_data->get( 'id' ), 'course-category', '', ',' ) ) printf( __( 'in %s' ), $course_cats ); ?>
							</div>
						</div>
						<div class="course-summery">
							<div class="course-rating"><?php echo coschool_populate_stars( $course_data->get( 'rating' ) ); ?></div>
							<div class="course-price"><?php esc_html_e( coschool_price( $course_data->get_price() ) ); ?></div>
						</div>
					</div>
					
					<div class="course-footer">
						<a class="course-enrol-btn" href="<?php echo esc_url( $course_data->get( 'purchase_url' ) ) ?>"><?php esc_html_e( $course_data->get( 'enroll_label' ) ); ?></a>
					</div>
				</div>				
			<?php endforeach; ?>
	</div>
	<div class="coschool-dashboard-pagination">
		<?php 
			if ( $page_count > $limit ) {
				coschool_paginated( $wishlist_url, $page_count, $paginat_number ); 
			}
		?>
	</div>
</div>