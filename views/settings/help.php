<?php
$base_url 	= coschool_site_url();
$buttons 	= [
	'changelog' => [
		'url' 	=> 'https://wordpress.org/plugins/coschool/#developers',
		'label' => __( 'Changelog', 'coschool' ) 
	],
	'community' 	=> [
		'url' 	=> 'https://facebook.com/groups/pluggable.io',
		'label' => __( 'Community', 'coschool' ) 
	],
	'website' 	=> [
		'url' 	=> 'https://pluggable.io/coschool/',
		'label' => __( 'Official Website', 'coschool' ) 
	],
	'support' 	=> [
		'url' 	=> 'https://help.pluggable.io/',
		'label' => __( 'Ask Support', 'coschool' ) 
	],
];
$buttons 	= apply_filters( 'coschool_help_btns', $buttons );
?>

<div class="coschool-help-tab">
	<div class="coschool-documentation">
		 <div class='wrap'>
		 	<div id='coschool-helps'>
		    <?php

		    $helps 	= get_option( 'coschool_docs_json', [] );
			$utm 	= [ 'utm_source' => 'dashboard', 'utm_medium' => 'settings', 'utm_campaign' => 'faq' ];
		    if( is_array( $helps ) ) :
		    foreach ( $helps as $help ) {
		    	$help_link = add_query_arg( $utm, $help['link'] );
		        ?>
		        <div id='coschool-help-<?php esc_attr_e( $help['id'] ); ?>' class='coschool-help'>
		            <h2 class='coschool-help-heading' data-target='#coschool-help-text-<?php esc_attr_e( $help['id'] ); ?>'>
		                <a href='<?php echo esc_url( $help_link ); ?>' target='_blank'>
		                <span class='dashicons dashicons-admin-links'></span></a>
		                <span class="heading-text"><?php echo esc_html( $help['title']['rendered'] ); ?></span>
		            </h2>
		            <div id='coschool-help-text-<?php esc_attr_e( $help['id'] ); ?>' class='coschool-help-text' style='display:none'>
		                <?php echo wp_kses_post( wpautop( wp_trim_words( $help['content']['rendered'], 55, " <a class='sc-more' href='" . esc_url( $help_link ) . "' target='_blank'>[more..]</a>" ) ) ); ?>
		            </div>
		        </div>
		        <?php
		    }
		    else:
		        esc_html_e( 'Something is wrong! No help found!', 'coschool' );
		    endif;
		    ?>
		    </div>
		</div>
	</div>
	<div class="coschool-help-links">
		<?php 
		foreach ( $buttons as $key => $button ) {
			$button_url = add_query_arg( $utm, $button['url'] );
			echo "<a target='_blank' href='" . esc_url( $button_url ) . "' class='coschool-help-link'>" . esc_html( $button['label'] ) . "</a>";
		}
		?>
	</div>
</div>

<?php do_action( 'coschool_help_tab_content' ); ?>