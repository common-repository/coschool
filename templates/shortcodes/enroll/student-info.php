<?php
use Codexpert\CoSchool\App\Student\Data as Student_Data;
$first_name = $last_name = $email = $readonly = '';

if( is_user_logged_in() ) {
	$student_data	= new Student_Data( get_current_user_id() );
	$first_name		= $student_data->get( 'firstname' );
	$last_name		= $student_data->get( 'lastname' );
	$email			= $student_data->get( 'email' );
	$readonly		= 'readonly';
}
?>
	<div class="coschool-student-info">
		<fieldset>
		    <legend><?php esc_html_e( 'First Name', 'coschool' ); ?></legend>
		    <input type="text" id="first_name" name="first_name" value="<?php esc_attr_e( $first_name ); ?>" <?php esc_attr_e( $readonly ); ?> required />
		    <span class="required"><i class="fas fa-exclamation-triangle"></i> <?php esc_html_e( 'Required Name', 'eschool' ); ?></span>
		</fieldset>

		<fieldset>
		    <legend><?php esc_html_e( 'Last Name', 'coschool' ); ?></legend>
		    <input type="text" id="last_name" name="last_name" value="<?php esc_attr_e( $last_name ); ?>" <?php esc_attr_e( $readonly ); ?>>
		</fieldset>

		<fieldset>
		    <legend><?php esc_html_e( 'Email', 'coschool' ); ?></legend>
		    <input type="email" id="email" name="email" value="<?php esc_attr_e( $email ); ?>" <?php esc_attr_e( $readonly ); ?> required />
		    <span class="required"><i class="fas fa-exclamation-triangle"></i> <?php esc_html_e( 'Required Email', 'eschool' ); ?></span>
		</fieldset>

		<?php if( ! is_user_logged_in() ) : ?>
		<fieldset>
		    <legend><?php esc_html_e( 'Password', 'coschool' ); ?></legend>
		    <input type="password" id="password" name="password" value="" required />
		    <span class="required"><i class="fas fa-exclamation-triangle"></i> <?php esc_html_e( 'Required Password', 'eschool' ); ?></span>
		</fieldset>
		<?php endif; ?>

	</div>