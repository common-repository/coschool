<?php 
$student_data 		= new \Codexpert\CoSchool\App\Student\Data( get_current_user_id() );

$user_login 		= $student_data->get( 'username' );
$user_email 		= $student_data->get( 'email' );
$user_firstname 	= $student_data->get( 'firstname' );
$user_lastname 		= $student_data->get( 'lastname' );
$display_name 		= $student_data->get( 'name' );
$user_id 			= $student_data->get( 'id' );

$coschool_avatar 	= get_user_meta( $user_id, '_coschool_avatar', true );
$phone_number 		= get_user_meta( $user_id, 'phone_number', true );
$login_info 		= get_user_meta( $user_id, 'login_info', true ) ? : [];
?>

<div class="coschool-dashboard-header">
	<h2 class="coschool-dashboard-title"><?php esc_html_e( 'My Profile', 'coschool' ); ?></h2>
</div>
<div class="coschool-dashboard-body">
	<div class="coschool-dashboard-profile-wrap">
		<div class="coschool-dashboard-profile-info">
			<form action="" id="coschool-dashboard-profile-form">
				<input type="hidden" name="action" value="update-profile">
				<?php wp_nonce_field( 'coschool' ); ?>
				<div class="coschool-form-section coschool-avatar-panel">
					<div class="coschool-form-inner-section">
						<input type="hidden" name="image_url" id="image_url" class="regular-text" value="<?php echo esc_url( $coschool_avatar ); ?>">
					    <div id="coschool-avatar">
					    	<img src="<?php echo esc_url( $student_data->get_avatar_url() ); ?>" alt="<?php echo esc_attr( $display_name ); ?>">
					    	<button type="button" name="upload-btn" id="coschool-upload-btn"><i class="fas fa-pen"></i></button>
					    </div>
					</div>
				</div>
				<div class="coschool-form-section grid-2">
					<div class="coschool-form-inner-section">
						<label for=""><?php esc_html_e( 'First Name', 'coschool' ); ?></label>
						<input type="text" name="first_name" placeholder="<?php esc_attr_e( 'Your First Name', 'coschool' ); ?>" value="<?php echo esc_attr( $user_firstname ); ?>">
					</div>
					<div class="coschool-form-inner-section">
						<label for=""><?php esc_html_e( 'Last Name', 'coschool' ); ?></label>
						<input type="text" name="last_name" placeholder="<?php esc_attr_e( 'Your Last Name', 'coschool' ); ?>" value="<?php echo esc_attr( $user_lastname ); ?>">
					</div>
				</div>
				<div class="coschool-form-section">
					<div class="coschool-form-inner-section">
						<label for=""><?php esc_html_e( 'Email', 'coschool' ); ?></label>
						<input type="email" name="email" placeholder="<?php esc_attr_e( 'Your Email', 'coschool' ); ?>" value="<?php echo esc_attr( $user_email ); ?>">
					</div>
				</div>
				<div class="coschool-form-section">
					<div class="coschool-form-inner-section">
						<label for=""><?php esc_html_e( 'Phone Number', 'coschool' ); ?></label>
						<input type="number" name="phone_number" placeholder="<?php esc_attr_e( 'Your Phone Number', 'coschool' ); ?>" value="<?php echo esc_attr( $phone_number ); ?>">
					</div>
				</div>
				<div class="coschool-form-section grid-2">
					<div class="coschool-form-inner-section">
						<label for=""><?php esc_html_e( 'Password', 'coschool' ); ?></label>
						<input type="password" name="pass1" placeholder="<?php esc_attr_e( 'Password', 'coschool' ); ?>">
					</div>
					<div class="coschool-form-inner-section">
						<label for=""><?php esc_html_e( 'Confirm Password', 'coschool' ); ?></label>
						<input type="password" name="pass2" placeholder="<?php esc_attr_e( 'Confirm Password', 'coschool' ); ?>">
					</div>
				</div>
				<div class="coschool-form-section grid-2">
					<div class="coschool-form-inner-section">
						<div class="coschool-response-message" style="display: none;"></div>
					</div>
					<div class="coschool-form-inner-section action-panel">
						<input type="submit" value="<?php esc_html_e( 'Update Setting', 'coschool' ); ?>">
					</div>
				</div>
			</form>
		</div>
		<div class="coschool-dashboard-profile-login-log">
			<h3><?php esc_html_e( 'Latest Logins', 'coschool' ); ?></h3>
			<table class="coschool-login-log-table">
			    <thead>
			        <tr>
			            <th><?php esc_html_e( 'Date', 'coschool' ); ?></th>
			            <th><?php esc_html_e( 'IP', 'coschool' ); ?></th>
			        </tr>
			    </thead>
			    <tbody>
			        <?php
			        $format = get_option( 'links_updated_date_format' );
			        foreach ( $login_info as $time => $ip ):
			    		$time = wp_date( $format, $time );
			    		echo "<tr>
				            <td>" . esc_html( $time ) . "</td>
				            <td>" . esc_html( $ip ) . "</td>
				        </tr>";
			    	endforeach; ?>
			    </tbody>
			</table>
		</div>
	</div>
</div>