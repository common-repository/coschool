<?php
	if ( ! is_user_logged_in() ) {
		?>
		<div class="coschool-login-form-container coschool-authentication">
			<?php wp_login_form( [ 'form_id' => 'coschool-login-form' ] ); ?>
		</div>
		<?php
	}
	else {
		esc_html_e( 'You are already logged in', 'coschool' );
	}
?>
