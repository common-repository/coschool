<?php 
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Course\Data;

global $post;

$course_data 	= new Data( $post->ID );
$banner_id 		= $course_data->get( 'course_banner' );

$display 		= 'none';
$uploader 		= '';

if ( ! is_null( $banner_id ) ) {
	$display 	= '';
	$uploader 	= 'none';
}
?>

<div id="coschool-course-banner-wrap">
	<input type="hidden" id="coschool-course-banner" name="course_banner" value="<?php echo esc_attr( $banner_id ); ?>">
	<div id="coschool-course-banner-preview" style="display: <?php echo esc_attr( $display ); ?>;">
		<?php
		$banner_meta 	= wp_get_attachment_metadata( $banner_id );
		if( isset( $banner_meta['mime_type'] ) && false !== strpos( $banner_meta['mime_type'], 'video' ) ) {
			$video_url 	= wp_get_attachment_url( $banner_id );
			
			echo "
			<video class='coschool-video coschool-video-banner' width='100%' height='' controls>
				<source src='" . esc_url( $video_url ) . "' type='video/mp4'>
				<source src='" . esc_url( $video_url ) . "' type='video/ogg'>
			</video>";
		}
		else {
		?>
		<img src="<?php echo esc_url( wp_get_attachment_image_url( $banner_id, 'coschool-banner' ) ); ?>">
		<?php } ?>
	</div>
	<div class="coschool-course-banner-uploader" style="display: <?php esc_attr_e( $uploader ); ?>;">
		<span class="coschool-banner-uploader-btn dashicons dashicons-cloud-upload"></span>
		<p><?php esc_html_e( 'Upload', 'coschool' ); ?></p>
	</div>
	<div class="coschool-course-banner-cancel" style="display: <?php esc_attr_e( $display ); ?>;">
		<span class="coschool-banner-cancel-btn"><?php esc_html_e( 'Remove Banner', 'coschool' ); ?></span>
	</div>
</div>