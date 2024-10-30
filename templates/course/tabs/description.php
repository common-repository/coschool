<?php
global $course_data;

the_content();
?>
<div class="coschool-cs-content">
	<h2 class="coschool-cs-content-title"><?php _e( 'Content', 'coschool' ); ?></h2>
	<?php do_action( 'coschool_course_progress', $course_data->get_contents() ); ?>
</div>