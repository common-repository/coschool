<?php
$addons = [
	'certificate'	=> [
		'title'		=> __( 'Certificate Builder', 'coschool' ),
		'desc'		=> __( 'Drag & Drop certificate builder for CoSchool with ready-made templates included.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/certificate-builder',
		'class'		=> 'Certificate',
	],
	'course-bundle'	=> [
		'title'		=> __( 'Course Bundle', 'coschool' ),
		'desc'		=> __( 'Make bundle of courses and sell all at once.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/course-bundle',
		'class'		=> 'Course_Bundle',
		'is_free'	=> true,
	],
	'multi-instructor'	=> [
		'title'		=> __( 'Multi Instructor', 'coschool' ),
		'desc'		=> __( 'Allow others to sell courses from your site.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/multi-instructor',
		'class'		=> 'Multi_Instructor',
	],
	'assignment'	=> [
		'title'		=> __( 'Assignment', 'coschool' ),
		'desc'		=> __( 'Add assignments to your courses and allow students to participate.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/assignment',
		'class'		=> 'App\\Assignment',
		'is_free'	=> true,
	],
	'stripe'		=> [
		'title'		=> __( 'Stripe Payment', 'coschool' ),
		'desc'		=> __( 'Stripe Payment gateway integration for CoSchool.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/stripe-payment',
		'class'		=> 'Stripe',
	],
	'square'		=> [
		'title'		=> __( 'Square Payment', 'coschool' ),
		'desc'		=> __( 'Square Payment gateway integration for CoSchool.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/square-payment',
		'class'		=> 'Square',

	],
	'fastspring'	=> [
		'title'		=> __( 'FastSpring Payment', 'coschool' ),
		'desc'		=> __( 'FastSpring Payment gateway integration for CoSchool.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/fastspring-payment',
		'class'		=> 'FastSpring',
	],
	'2checkout'		=> [
		'title'		=> __( '2Checkout Payment', 'coschool' ),
		'desc'		=> __( '2Checkout Payment gateway integration for CoSchool.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/2checkout-payment',
		'class'		=> 'TwoCheckout',
	],
	'wc'			=> [
		'title'		=> __( 'WooCommerce Integration', 'coschool' ),
		'desc'		=> __( 'WooCommerce Payment gateway integration for CoSchool.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/woocommerce-integration',
		'class'		=> 'WC',
	],
	'edd'			=> [
		'title'		=> __( 'Easy Digital Downloads Integration', 'coschool' ),
		'desc'		=> __( 'Easy Digital Downloads Payment gateway integration for CoSchool.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/easy-digital-downloads-integration',
		'class'		=> 'EDD',
	],
	'pmpro'			=> [
		'title'		=> __( 'Paid Memberships Pro Integration', 'coschool' ),
		'desc'		=> __( 'Paid Memberships Pro Payment gateway integration for CoSchool.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/paid-memberships-pro-integration',
		'class'		=> 'PMPro'

	],
	'smtp'			=> [
		'title'		=> __( 'Custom SMTP', 'coschool' ),
		'desc'		=> __( 'Add custom SMTP to send Mail.', 'coschool' ),
		'url'		=> 'https://pluggable.io/coschool/add-ons/custom-smtp',
		'class'		=> 'SMTP',
		'is_free'	=> true,
	],
];
?>

<div class="wrap">
	<h2><?php esc_html_e( 'Add-ons', 'coschool' ); ?></h2>
	<div id="coschool-addons">
		<div id="coschool-addon_all-access" class="coschool-addon">
			<div class="coschool-addon_thumb">
				<img src="<?php echo esc_url( COSCHOOL_ASSET . "/img/add-ons/all-access.png" ); ?>" />
			</div>
			<div class="coschool-addon_content">
				<h2><a href="https://pluggable.io/coschool" target="_blank"><?php esc_html_e( 'All Access Pass', 'coschool' ); ?></a></h2>
				<p><?php esc_html_e( 'Access to all premium add-ons. Altogether.' ); ?></p>
				<div class="coschool-addon-action"><a href="https://pluggable.io/coschool/add-ons/all-access/" target="_blank"><?php esc_html_e( 'Learn more..', 'coschool' ); ?></a></div>
			</div>
		</div>
		<?php
		foreach ( $addons as $id => $addon ) :
			$url 		= isset( $addon['url'] ) ? $addon['url'] : "https://pluggable.io/coschool/add-ons/{$id}";
			$is_free	= isset( $addon['is_free'] ) && $addon['is_free'];
			$action 	= sprintf( '<a href="%1$s" target="_blank">%2$s</a>', esc_url( $url ), __( 'Learn more..', 'coschool' ) );
			$badge		= $is_free ? __( 'Free Add-on', 'coschool' ) : '';
			$css_class	= $is_free ? 'free inactive' : 'pro inactive';

			if( isset( $addon['class'] ) && class_exists( "Codexpert\\CoSchool\\{$addon['class']}\\Plugin" ) ) {

				$class		= "Codexpert\\CoSchool\\{$addon['class']}\\Helper";
				$helper		= new $class;
				$license	= $helper::license();

				if( ! $is_free ) {
					if( ! $license->_is_activated() ) {
						$action		= sprintf( '<a class="coschool-addon-activated" href="%1$s">%2$s</a>', $license->get_activation_url(), __( 'Activate License', 'coschool' ) );
						$badge		= __( 'Not Licensed', 'coschool' );
						$css_class	= 'pro active not-licensed';
					}
					else {
						$action		= sprintf( '<a class="coschool-addon-deactivated" href="%1$s">%2$s</a>', $license->get_deactivation_url(), __( 'Dectivate License', 'coschool' ) );
						$badge		= __( 'Licensed', 'coschool' );
						$css_class	= 'pro active licensed';
					}
				}
				else {
					$action 	= '';
					$badge		= __( 'Free Add-on', 'coschool' );
					$css_class	= 'free active';
				}
			}

			printf( '
				<div id="coschool-addon_%1$s" class="coschool-addon %2$s">
					<span class="coschool-addon_badge">%3$s</span>
					<span class="coschool-status-badge">&check;</span>
					<div class="coschool-addon_thumb">
						<img src="%4$s" />
					</div>
					<div class="coschool-addon_content">
						<h2><a href="%5$s" target="_blank">%6$s</a></h2>
						<p>%7$s</p>
						<div class="coschool-addon-action">%8$s</div>
					</div>
				</div>',
				$id,
				$css_class,
				$badge,
				COSCHOOL_ASSET . "/img/add-ons/{$id}.png",
				$url,
				$addon['title'],
				$addon['desc'],
				$action,
			);
		endforeach;
		?>
	</div>
</div>