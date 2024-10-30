<?php
namespace Codexpert\CoSchool\App;
/**
 * The Template for displaying the content of a single course
 *
 * This template can be overridden by copying it to yourtheme/coschool/content-single-course.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $course_data, $instructor_data, $student_data;

$course_tabs = [
	'description' 	=> __( 'Description', 'coschool' ),
	'reviews' 	 	=> __( 'Reviews', 'coschool' ),
	// 'notice' 		=> __( 'Notice', 'coschool' ),
	'faq' 		 	=> __( 'FAQ', 'coschool' ),
];
$course_tabs 	= apply_filters( 'coschool_course-tabs', $course_tabs );
$wishlists 		= $student_data->get_wishlist();

$added 			= '';
$is_added 		= false;
if ( in_array( $course_data->get( 'id' ), $wishlists ) ) {
	$added 		= ' added';
	$is_added 	= true;
}

$crunchifyURL 	= urlencode( get_permalink() );
$crunchifyTitle = htmlspecialchars( urlencode( html_entity_decode( get_the_title(), ENT_COMPAT, 'UTF-8') ), ENT_COMPAT, 'UTF-8');
$twitterURL   	= add_query_arg( [ 'url' 	=> $crunchifyURL ], 'https://twitter.com/intent/tweet' );
$facebookURL  	= add_query_arg( [ 'u' 	=> $crunchifyURL ], 'https://www.facebook.com/sharer.php' );
$linkedInURL  	= add_query_arg( [ 'url' 	=> $crunchifyURL, 'title' => $crunchifyTitle ], 'https://www.linkedin.com/shareArticle' );
$whatsappURL  	= add_query_arg( [ 'text' => $crunchifyURL ], 'https://wa.me/' );

?>

<div id="course-<?php the_ID(); ?>" class="coschool-course-single coschool-singular-grid">
	<div class="coschool-cs-details">
		<h1 class="coschool-cs-title coschool-title"><?php the_title(); ?></h1>
		<!-- <div class="coschool-cs-categories"><?php echo get_the_term_list( $course_data->get( 'id' ), 'course-category', '<div class="coschool-cs-category">', ' ', '</div>' ); ?></div> -->
		<div class="coschool-cs-info coschool-meta">

			<div class="coschool-cs-update coschool-meta-item">
				<div class="coschool-cs-info-label"><?php esc_html_e( 'Last Updated', 'coschool' ); ?></div>
				<div class="coschool-cs-info-data"><?php echo date( 'M Y', strtotime( $course_data->get( 'updated' ) ) ); ?></div>
			</div>

			<?php if( $category = get_the_term_list( $course_data->get( 'id' ), 'course-category', '', ', ', '' ) ) : ?>
			<div class="coschool-cs-level coschool-meta-item">
				<div class="coschool-cs-info-label"><?php esc_html_e( 'Category', 'coschool' ); ?></div>
				<div class="coschool-cs-info-data"><?php echo wp_kses_post( $category ); ?></div>
			</div>
			<?php endif; ?>

			<?php if( $language = get_the_term_list( $course_data->get( 'id' ), 'course-language', '', ', ', '' ) ) : ?>
			<div class="coschool-cs-language coschool-meta-item">
				<div class="coschool-cs-info-label"><?php esc_html_e( 'Language', 'coschool' ); ?></div>
				<div class="coschool-cs-info-data"><?php echo wp_kses_post( $language ); ?></div>
			</div>
			<?php endif; ?>

			<?php if( $difficulty = get_the_term_list( $course_data->get( 'id' ), 'course-difficulty', '', ', ', '' ) ) : ?>
			<div class="coschool-cs-level coschool-meta-item">
				<div class="coschool-cs-info-label"><?php esc_html_e( 'Level', 'coschool' ); ?></div>
				<div class="coschool-cs-info-data"><?php echo wp_kses_post( $difficulty ); ?></div>
			</div>
			<?php endif; ?>

			<div class="coschool-cs-students coschool-meta-item">
				<div class="coschool-cs-info-label"><?php esc_html_e( 'Students', 'coschool' ); ?></div>
				<div class="coschool-cs-info-data"><?php esc_html_e( count( $course_data->get( 'students' ) ) ); ?></div>
			</div>

			<div class="coschool-cs-share coschool-meta-item">
				
				<?php if( is_user_logged_in() && ! $student_data->has_course( $course_data->get( 'id' ) ) ) : ?>

				<a class="coschool-wishlist-btn<?php echo esc_attr( $added ); ?>" 
				   data-course_id="<?php echo esc_attr( $course_data->get( 'id' ) ); ?>" 
				   data-added="<?php echo esc_attr( $is_added ); ?>" 
				   href="">
				   <i class="far fa-heart"></i>
				</a>

				<?php endif; ?>
				<div class="coschool-share-buttons">
					<ul>
						<li><a class="facebook" target="_blank" href="<?php echo esc_url( $facebookURL ); ?>"><i class="fab fa-facebook-f"></i></a></li>
						<li><a class="twitter" target="_blank" href="<?php echo esc_url( $twitterURL ); ?>"><i class="fab fa-twitter"></i></a></li>
						<li><a class="linkedin" target="_blank" href="<?php echo esc_url( $linkedInURL ); ?>"><i class="fab fa-linkedin-in"></i></a></li>
						<li><a class="whatsapp" target="_blank" href="<?php echo esc_url( $whatsappURL ); ?>"><i class="fab fa-whatsapp"></i></a></li>
					</ul>
				</div>
				<a class="coschool-share-button" href=""><i class="fas fa-share-alt"></i></a>
			</div>
		</div>
		<div class="coschool-cs-thumbnail">
			<?php echo wp_kses_post( $course_data->get_banner() ); ?>
		</div>
		<div class="coschool-cs-tabs coschool-tabs">
			<div class="coschool-cs-tab-items coschool-tab-items">
				<ul>
				<?php 
				$fisrt_item = array_key_first( $course_tabs );
				foreach ( $course_tabs as $key => $label ) {
					$active = $fisrt_item == $key ? 'active' : ''; 
					echo '<li class="coschool-cs-tab-item coschool-tab-item '. $active . '" data-tab="'. esc_attr( $key ) .'">'. esc_html( $label ) .'</li>';
				}
				?>
				</ul>
			</div>
			<div class="coschool-cs-tab-content coschool-tab-contents">
				<?php 
				foreach ( $course_tabs as $key => $label ) {
					$active = $fisrt_item == $key ? 'active' : ''; 
					echo "<div id='coschool-tab-content-" . esc_attr( $key ) . "' class='coschool-tab-content " . esc_attr( $active ) . "'>";
					
						do_action( "coschool_course_tab_content_{$key}" );
					
					echo "</div>";
				}
				?>
			</div>
		</div>
	</div>
	<div class="coschool-cs-sidebar">
		<div class="coschool-cs-summery">
			<div class="coschool-cs-summery-section coschool-cs-author-section">
				<h3 class="coschool-singular-title-sm"><?php esc_attr_e( 'About the Instructor', 'coschool' ) ?></h3>
				<div class="coschool-cs-author">
					<div class="coschool-cs-author-image">
						<img src="<?php echo esc_url( $instructor_data->get_avatar_url() ); ?>" alt="<?php esc_attr_e( $instructor_data->get( 'name' ) ) ?>">
					</div>
					<div class="coschool-cs-author-info">
						<h2 class="coschool-cs-author-name">
							<a href="<?php echo esc_url( $instructor_data->get( 'archive_url' ) ); ?>"><?php esc_html_e( $instructor_data->get( 'name' ) ) ?></a>
						</h2>
					</div>
				</div>
				<div class="coschool-cs-author-summery">
					<div class="coschool-cs-author-rating"><?php echo ( $rating = $instructor_data->get_rating() ) . coschool_populate_stars( $rating ); ?></div>
					<div class="coschool-cs-author-courses"><img src="<?php echo esc_url( coschool_get_icon( 'lesson' ) ); ?>"><?php printf( __( ' %d Courses', 'coschool' ), count( $instructor_data->get_courses() ) ); ?></div>
				</div>
			</div>
			<div class="coschool-cs-summery-section coschool-cs-about">
				<h3 class="coschool-singular-title-sm"><?php esc_attr_e( 'About the Course', 'coschool' ); ?></h3>
				<ul>
					<?php 
						printf( '<li><img src="%s"> %d %s</li>',
							esc_url( coschool_get_icon( 'lesson' ) ),
							count( $course_data->get_lessons() ),
							__( 'Lessons', 'coschool' )
						);

						printf( '<li><img src="%s"> %d %s</li>',
							esc_url( coschool_get_icon( 'quiz' ) ),
							count( $course_data->get_quizzes() ),
							__( 'Quizzes', 'coschool' )
						);

						printf( '<li><img src="%s"> %d %s</li>',
							esc_url( coschool_get_icon( 'assignment' ) ),
							count( $course_data->get_assignments() ),
							__( 'Assignments', 'coschool' )
						);

						printf( '<li><img src="%s"> %s</li>',
							esc_url( coschool_get_icon( 'certificate' ) ),
							__( 'Certificate of completion', 'coschool' )
						);

						printf( '<li><img src="%s"> %s</li>',
							esc_url( coschool_get_icon( 'lifetime-access' ) ),
							__( 'Lifetime access', 'coschool' )
						);
					?>
				</ul>
			</div>
			<div class="coschool-cs-summery-section coschool-cs-enrol">
				
				<?php if( ! $student_data->has_course( $course_data->get( 'id' ) ) ) : ?>
					<div class="coschool-cs-price"><?php esc_html_e( coschool_price( $course_data->get_price() ) ); ?></div>
				<?php endif; ?>

				<a href="<?php echo esc_url( $course_data->get( 'purchase_url' ) ); ?>" class="coschool-cs-enrol-btn"><?php esc_html_e( $course_data->get( 'enroll_label' ) ); ?></a>
			</div>
		</div>
	</div>
</div>