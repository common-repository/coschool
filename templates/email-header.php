<?php
namespace Codexpert\CoSchool\App;
use Codexpert\CoSchool\Helper;

/**
 * The Template for displaying the content of the email header
 *
 * This template can be overridden by copying it to yourtheme/coschool/email-header.php.
 *
 * @package		Codexpert\CoSchool\Template
 * @version     0.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
</head>
<body style="background: #f4f4f4; padding-top: 30px; padding-bottom: 50px;">
	<div class="wrap" style="width: 600px; margin: 0 auto;">
		<div id="header-wrapper" style="text-align: center; padding: 20px;">
			<img src="<?php echo Helper::get_option( 'coschool_email', 'logo', COSCHOOL_ASSET . '/img/logo.png' ); ?>" alt="<?php _e( 'CoSchool', 'coschool' ); ?>" />
		</div>
		<div id="content-wrapper" style="background: white;">
			<div id="header" style="height: 172px; background: url(<?php echo Helper::get_option( 'coschool_email', 'banner', COSCHOOL_ASSET . '/img/email-banner.png' ); ?>); line-height: 172px; text-align: center;">
				<h2 style="display: inline-block; vertical-align: middle; line-height: normal; font-family: 30px; color: #fff;"><?php esc_html_e( $args['subject'] ); ?></h2>
			</div>
			<div id="content" style="padding: 40px 30px; font-size: 16px;">