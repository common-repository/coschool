<?php
// namespace Codexpert\CoSchool\App\Assignment;
use Codexpert\CoSchool\Helper;

/**
 * The Template for displaying the content of a single assignment
 *
 * This template can be overridden by copying it to yourtheme/coschool/content-single-assignment.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


global $assignment_data, $student_data, $course_data;


$is_completed 	= $student_data->has_completed( $assignment_data->get( 'ID' ) ) ? 'coschool-green' : 'coschool-ash';
$type_text 		= $student_data->has_completed( $assignment_data->get( 'ID' ) ) ? __( 'Completed', 'coschool' ) : __( 'Incomplete', 'coschool' );
$attachments	= ! is_null( $submission = $assignment_data->get_submission( $student_data->get( 'ID' ) ) ) ? coschool_unserialize( $submission->reference ) : [];
$prerequisites 	= $student_data->has_prerequisites( $assignment_data->get( 'id' ) );
$course_url 	= get_the_permalink( $course_data->get( 'id' ) );

?>

<div id="assignment-<?php the_ID(); ?>" class="coschool-singular-grid">
	<div class="coschool-assignment-details">
		<h2 class="coschool-parent-course-title"><a href="<?php echo esc_url( $course_url ); ?>"><?php echo $course_data->get('title'); ?></a></h2>
		<h1 class="coschool-singular-title"><?php the_title(); ?></h1>
		<p class="coschool-assignment-desc"></p>
		<?php if ( coschool_has_access( get_the_ID() ) && ! $prerequisites ): ?>
		<div class="coschool-assignment-instruction">
			<div class="coschool-assignment-metas coschool-meta">
				<div class="coschool-meta-item"><span><i class="fas fa-calendar-week"></i></span>Due Sunday, February 6 by 11:59pm</div>
				<div class="coschool-meta-item coschool-content-status <?php esc_attr_e( $is_completed ); ?>">
					<?php echo "<span class='{$is_completed}'>{$type_text}</span>"; ?>
				</div>
			</div>
			<div class="coschool-assignment-content">
				<h2><?php _e( 'Instruction', 'coschool' ); ?></h2>
				<?php the_content(); 

				if( $student_data->has_completed( $assignment_data->get( 'id' ) ) ) : ?>
					<div class="coschool-assignment-upload-list">
						<ul>
							<?php 
							foreach ( $attachments as $attachment_id ):
								$filename 	= basename ( get_attached_file( $attachment_id ) );
								$file_path 	= wp_get_attachment_url( $attachment_id );
								echo "<li>
									{$filename}
									<a href='{$file_path}' download> <span class='coschool-assignment-download'><i class='fas fa-download'></i></span>
									</a>
								</li>";
							endforeach; 
							?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		</div>

			<?php if( ! $student_data->has_completed( $assignment_data->get( 'id' ) ) ) : ?>
			<div class="coschool-assignment-buttton-panel">
				<form action="" id="coschool-assignment-submit-form">
					<?php wp_nonce_field( 'coschool' ); ?>
					<input type="hidden" name="action" value="coschool-submit-assignment">
					<input type="hidden" name="assignment_id" value="<?php echo esc_attr( $assignment_data->get( 'id' ) ); ?>">
					<div class="coschool-assignment-upload-panel">
						<label for=""><?php _e( 'Upload your files', 'coschool' ); ?></label>
						<button class="coschool-upload-btn">
							<span>
								<i class="fas fa-cloud-download"></i>
								<?php _e( 'Drag a file here or browse to upload.', 'coschool' ); ?>
							</span>
						</button>
					</div>
					<div class="coschool-assignment-upload-list">
						<ul></ul>
					</div>
					<button class="coschool-submit-assignment" type="submit" data-content_id="<?php echo $assignment_data->get( 'id' ); ?>" disabled><?php _e( 'Submit Assignment', 'coschool' ); ?></button>
				</form>
			</div>
			<?php endif; ?>

		<?php elseif ( coschool_has_access( get_the_ID() ) && $prerequisites ) : ?>
		<div class="coschool-prerequisites-container">
			<?php do_action( 'coschool_prerequisites', $prerequisites, $assignment_data ); ?>
		</div>
		<?php else: the_content(); ?>
		<?php endif; ?>
	</div>
	
	<div class="coschool-course-progress">		
		<h3 class="coschool-course-progress-title"><?php _e( 'Course Content', 'coschool' ) ?></h3>
		<?php do_action( 'coschool_course_progress', $assignment_data->get_course() ); ?>
	</div>
</div>