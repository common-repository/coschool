<?php
global $course_data;

$faqs = $course_data->get( 'course_faq' );
unset( $faqs['count'] );
?>
<div class="coschool-cs-faqs-section">
	<div class="coschool-cs-faqs coschool-accordions">
		<?php
			if ( !empty( $faqs ) ) {
				foreach ( $faqs as $faq ):
				?>
			 	<div class="coschool-accordion">
			 		<div class="coschool-accordion-header">
			 			<div class="coschool-accordion-title">
			 				<?php esc_html_e( $faq['question'] ); ?>
		 				</div>
		 				<i class="fas fa-minus indicator"></i>
		 				<i class="fas fa-plus indicator"></i>
			 		</div>
			 		<div class="coschool-accordion-body" style="display:none;">
			 			<?php echo wp_kses_post( $faq['answer'] ); ?>
			 		</div>
			 	</div>
				<?php 
				endforeach;
			}
			else {
				_e( 'No FAQ Found!', 'eschool' );
			}
		?>
	</div>
</div>