<div id="coschool-student-dashboard" class="coschool-dashboard">
	<div class="coschool-dashboard-menubar">
		<?php do_action( 'coschool_student_dashboard_navbar' ); ?>
	</div>
	<div class="coschool-dashboard-content">
		<?php 
		$current_tab 	= get_query_var( 'coschool_dtab' );
		$tab 			=  $current_tab ? esc_html( $current_tab ) : 'summary';
		do_action( "coschool_student_dashboard_content_{$tab}" );
		?>
	</div>
</div>