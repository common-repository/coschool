<?php 
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Course\Data;

global $post;

wp_enqueue_script( 'jquery-ui-sortable' );
wp_enqueue_style( 'coschool-admin-course' );
wp_enqueue_script( 'coschool-admin-course' );

$contents = [
	'lesson'		=> Helper::get_posts( [ 'author' => get_current_user_id(), 'post_type' => 'lesson', 'post_status' => [ 'publish', 'draft' ] ] ),
	'quiz'			=> Helper::get_posts( [ 'author' => get_current_user_id(), 'post_type' => 'quiz', 'post_status' => [ 'publish', 'draft' ] ] ),
	'assignment'	=> Helper::get_posts( [ 'author' => get_current_user_id(), 'post_type' => 'assignment', 'post_status' => [ 'publish', 'draft' ] ] ),
];

$all_contents = apply_filters( 'coschool_all_contents', $contents );

$course_data 		= new Data( $post->ID );
$course_contents 	= $course_data->get( 'course_contents' );
?>

<div class="course-chapter-wrap">
<?php
if( ! is_null( $course_contents ) && count( $course_contents ) > 0 ) :
foreach ( $course_contents as $chapter => $contents ) { ?>
	<div class="course-content">
		<span class="remove-chapter"><span class="dashicons dashicons-no-alt"></span></span>
		<div class="course-chapter-input-wrap">
			<input type="text" name="course-chapter" class="course-chapter-input" value="<?php esc_attr_e( $chapter ); ?>" placeholder="<?php esc_attr_e( 'Chapter Name', 'coschool' ); ?>" required />
			<button class="course-btn-add-content" data-type="lesson"><?php esc_html_e( 'Add Content', 'coschool' ); ?></button>
		</div>
		
		<ul class="course-content-list">
			
			<?php
			foreach ( $contents as $id => $_content ) {
				$content = new Data( $_content );
				echo "
				<li class='course-content-item " . esc_attr( $content->get( 'post_type' ) ) . "'>
					<span class='dashicons dashicons-menu'></span>
					<span class='course-content-title'>" . esc_html( $content->get( 'name' ) ) . "</span>
					<div class='course-content-actions'>
						<a href='" . esc_attr( get_edit_post_link( $content->get( 'id' ) ) ) . "' target='_blank' class='dashicons dashicons-edit course-content-edit'></a>
						<a href='" . esc_url( get_permalink( $content->get( 'id' ) ) ) . "' target='_blank' class='dashicons dashicons-visibility course-content-view'></a>
						<a href='#' class='dashicons dashicons-no-alt course-content-remove'></a>
					</div>
					<input type='hidden' name='course_contents[" . esc_attr( $chapter ) . "][]' value='" . esc_attr( $content->get( 'id' ) ) . "'>
				</li>
				";
			}
			?>

		</ul>
	</div>

<?php }
endif; ?>
	<div class="course-add-chapter-wrap">
		<button class="course-add-chapter button button-primary button-hero"><?php esc_html_e( 'Add Chapter', 'coschool' ); ?></button>
	</div>
</div>

<!-- THE MODAL -->
<div id="course-content-modal" style="display: none;">
	<div class="course-content-content">
		<h2 id="course-content-title"><?php esc_html_e( 'Add Content', 'coschool' ); ?></h2>
		<span class="modal-close-btn dashicons dashicons-no-alt"></span>
		<div class="coschool-content-tabs">
			<?php 
				$_tabs = [
					'<button class="course-content-btn" data-type="lesson">'. __( 'Add Lesson', 'coschool' ) .'</button>',
					'<button class="course-content-btn" data-type="quiz">'. __( 'Add Quiz', 'coschool' ) .'</button>',
				];
				$tabs = apply_filters( 'coschool_content_tabs', $_tabs );

				foreach ( $tabs as $tab ) {
					echo wp_kses_post( $tab );
				}
			?>
		</div>
		<div class="modal-form-container">

			<?php  foreach ( $all_contents as $type => $content ): ?>
				<div id="<?php esc_attr_e( $type ) ?>-list-container" class="modal-list-container" style="display: none;">
					<select id="<?php esc_attr_e( $type ) ?>-list">
						<?php 
							if( ! empty( $content ) ):
								foreach ( $content as $item_id => $item_name ) {
									echo "<option value='". esc_attr( $item_id ) ."'>". esc_html( $item_name ) ."</option>";
								}
							endif;
						 ?>
					</select>
					<button class="add-new-item"><?php esc_html_e( 'Insert', 'coschool' ); ?></button>
				</div>
			<?php endforeach; ?>

			<div class="course-content-separator"><?php esc_html_e( 'OR', 'coschool' ); ?></div>
			<div id="course-content-form">
				<input type="hidden" name="content_type">
				<input id="new-item-title" type="text" name="content-title" placeholder="<?php esc_attr_e( 'Content Title', 'coschool' ); ?>">
				<button id="create-new-item" ><?php esc_html_e( 'Create and Insert' ); ?></button>
			</div>
			<div id="new-item-error-notice" style="display: none;"><?php esc_html_e( 'Title Can not be empty', 'coschool' ); ?></div>
		</div>
	</div>
</div>