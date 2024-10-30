<?php
global $student_data, $course_data;

$content_count 		= count( $course_data->get_contents() );
$progress_percent 	= $student_data->get_progress( $course_data->get( 'id' ) );
$complete_count		= round( $progress_percent * $content_count / 100 );
?>
<div class="coschool-course-progress-section">
	<?php if( coschool_has_access( $course_data->get( 'id' ) ) ) : ?>
	<div class="coschool-coures-progress-bar-panel">
		<div class="coschool-coures-progress-content">
			<h4><?php printf( __( '%d%s Completed', 'coschool' ), $progress_percent, '%' ); ?></h4>
			<h4><?php printf( __( '%d/%d', 'coschool' ), $complete_count, $content_count ); ?></h4>
		</div>
		<div class="coschool-coures-progress-bar" style="width: <?php echo esc_attr( $progress_percent ); ?>%"></div>
	</div>
	<?php endif; ?>
	<div class="coschool-coures-items">
		<?php 
			foreach ( $course_data->get_contents( '', true ) as $name => $contents ) {

				echo "<div class='coschool-accordion'>
						<div class='coschool-accordion-header'>
		 					<div class='coschool-accordion-title'>
		 					" . esc_html( $name ) . " 				
		 					<i class='fas fa-chevron-down indicator'></i>
			 			</div>
			 		</div>";

		 		echo "<div class='coschool-accordion-body'><ul>";

				foreach ( $contents as $content ) {

					if( ! isset( $content->ID ) ) continue;

					$content_id 	= $content->ID;
					$title 			= get_the_title( $content_id );
					$url 			= get_permalink( $content_id );
					$icon 			= coschool_get_icon( $content->post_type, true );
					$is_completed 	= $student_data->has_completed( $content_id ) ? 'coschool-green' : 'coschool-ash';
					$status_icon 	= coschool_has_access( $content->ID ) ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-lock"></i>';

					$current_item 	= '';
					if ( $content_id == get_the_ID() ) {
						$current_item = 'current-item';
					}
					echo "
						<li class='" . esc_attr( $current_item ) . "'>
							<a href='" . esc_url( $url ) . "'>
								<span>" . wp_kses_post( $icon ) . "</span>
								" . esc_html( $title ) . "
							</a>
							<span class='" . esc_attr( $is_completed ) . "'>
								" . wp_kses_post( $status_icon ) . "
							</span>
						</li>
						";
				}
				echo "</ul></div></div>";
			}
		?>
	</div>
</div>