<?php 
use Pluggable\Plugin\License;
use Codexpert\CoSchool\Helper;

global $coschool_pro;
$coschool_pro = new License( COSCHOOL_PRO, [
	'redirect'	=> admin_url( 'admin.php?page=coschool-pro' )
] );



?>

<div class="wrap">
	<h2><?php esc_html_e( 'Licence', 'coschool' ); ?></h2>
	<?php echo $coschool_pro->activation_form() ; ?>
</div>