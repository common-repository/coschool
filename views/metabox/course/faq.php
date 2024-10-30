<?php 
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Course\Data;

global $post;

$course_data 	= new Data( $post->ID );
$course_faqs 	= $course_data->get( 'course_faq' );

unset( $course_faqs['count'] );
?>

<div class="course-faq-wrap">
	<div class="course-faq-list">

		<?php
		if( ! is_null( $course_faqs ) && count( $course_faqs ) > 0 ) :

			$count = 0;
			foreach ( $course_faqs as $course_faq ) {
				?>
				<div class="course-faq-single-list">
					<h4><span class="dashicons dashicons-menu"></span><span class="course-faq-header"><?php echo esc_html( $course_faq['question'] ); ?></span> <button class="course-faq-remove"><span class="dashicons dashicons-no-alt"></span></button></h4>

					<div class="course-faq-single-content">
						<div class="course-faq-title">
							<label for=""><?php esc_html_e( 'Question', 'coschool' ); ?></label>
							<input type="text" name="course_faq[<?php echo esc_attr( $count ); ?>][question]" value="<?php echo esc_attr( $course_faq['question'] ); ?>">
						</div>
						<div class="course-faq-content">
							<label for=""><?php esc_html_e( 'Answer', 'coschool' ); ?></label>
							<?php wp_editor( $course_faq['answer'], 'course-faq-content-' . $count, [ 'textarea_name' => "course_faq[$count][answer]", 'textarea_rows' => 4 ] ); ?>
						</div>
					</div>
				</div>
			<?php $count++; }
		endif; ?>
		
	</div>
	<div class="course-add-faq-wrap">
		<button class="course-add-faq button button-primary button-hero"><?php esc_html_e( 'Add FAQ', 'coschool' ); ?></button>
	</div>

	<div class="course-faq-single-list course-faq-single-list-clone">
		<h4><span class="dashicons dashicons-menu"></span><span class="course-faq-header"><?php esc_html_e( 'FAQ', 'coschool' ); ?></span> <button class="course-faq-remove"><span class="dashicons dashicons-no-alt"></span></button></h4>

		<div class="course-faq-single-content">
			<div class="course-faq-title">
				<label for=""><?php esc_html_e( 'Question', 'coschool' ); ?></label>
				<input type="text" name="course_faq[count][question]" value="<?php esc_attr_e( 'FAQ', 'coschool' ); ?>">
			</div>
			<div class="course-faq-content">
				<label for=""><?php esc_html_e( 'Answer', 'coschool' ); ?></label>
				<?php wp_editor( '', 'course-faq-content', [ 'textarea_name' => "course_faq[count][answer]", 'textarea_rows' => 4 ] ); ?>
			</div>
		</div>
	</div>
</div>