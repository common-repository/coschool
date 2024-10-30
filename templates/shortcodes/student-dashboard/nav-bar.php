<?php 
$nav_items 		= coschool_student_dashboard_nav_items();
$student_data 	= new \Codexpert\CoSchool\App\Student\Data( get_current_user_id() );
$student_id 	= $student_data->get( 'id' );
$display_name 	= $student_data->get( 'name' );
?>
<div class="coschool-profile-card">
	<div class="coschool-avatar">
		<img src="<?php echo esc_url( $student_data->get_avatar_url() ); ?>" alt="<?php esc_attr_e( $display_name ); ?>">
	</div>
	<div class="coschool-name">
		<h2 class="coschool-user-name"><?php esc_html_e( $display_name ); ?></h2>
		<h4 class="coschool-user-id"><?php printf( __( 'Student ID #%d', 'coschool' ), $student_id ); ?></h4>
	</div>
</div>
<div class="coschool-nav-items">
	<ul>
		<?php foreach ( $nav_items as $key => $item ) {

			$active = '';
			if ( get_query_var( 'coschool_dtab' ) == $key ) {
				$active = ' active';
			}
			
			if ( isset( $item['label'] ) ) {
				$icon = isset( $item['icon'] ) ? $item['icon'] : '';
				$url  = $key != 'logout' ? coschool_dashoard_endpoint( $key ) : wp_logout_url( coschool_dashboard_page( true ) );

				echo '<li class="coschool-nav-item'. esc_attr( $active ) .'"><a href="' . esc_url( $url ) . '">' . wp_kses_post( $icon ) . esc_html( $item['label'] ) . '</a></li>';	
			}			
		}
		?>
	</ul>
</div>