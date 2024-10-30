<?php 
global $course_data;

$notices = $course_data->get_notices( true );
$format  = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
?>
<div class="coschool-cs-notices-section">
	<div class="coschool-cs-notices coschool-accordions">
		<?php
		foreach ( $notices as $notice ):
		?>
	 	<div class="coschool-accordion">
	 		<div class="coschool-accordion-header">
	 			<div class="coschool-accordion-title">
	 				<?php esc_html_e( $notice->post_title ); ?> <span class="coschool-notice-time"><?php echo date( $format, strtotime( $notice->post_date ) ); ?></span>
 				</div>
 				<i class="fas fa-chevron-right indicator"></i>
	 		</div>
	 		<div class="coschool-accordion-body" style="display:none;">
	 			<?php echo wp_kses_post( $notice->post_content ); ?>
	 		</div>
	 	</div>
		 <?php
		endforeach; ?>
	</div>
</div>