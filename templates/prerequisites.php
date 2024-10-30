<?php
use Codexpert\CoSchool\Helper;

global $student_data;
?>
<div class="coschool-prerequisites">
	<h2 class="coschool-prerequisites-title"><?php esc_html_e( 'You need to complete this topics first', 'coschool' ); ?></h2>
	<div class="coschool-prerequisites-contents">
		<ol>
			<?php
			foreach ( $prerequisites as $prerequisite ) :
				if ( ! $student_data->has_completed( $prerequisite ) ) {
					$title 	= get_the_title( $prerequisite );
					$url  	= get_the_permalink( $prerequisite );

					echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a></li>';
				}
			endforeach;
			?>
		</ol>
		<div class="coschool-prerequisites-thumbs">
			<img src="<?php echo esc_url( plugins_url( 'assets/img/prerequisites.png', COSCHOOL ) ); ?>" alt="<?php esc_html_e( 'Prerequisites', 'coschool' ); ?>">
		</div>
	</div>
</div>