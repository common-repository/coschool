<?php
/**
 * Email handler
 */
namespace Codexpert\CoSchool\App\Smtp;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Email {

	public function init() {
		add_action( 'phpmailer_init', [ $this, 'send_smtp_email' ] );
	}
	
	public function send_smtp_email( $phpmailer ) {

		if ( Helper::get_option( 'coschool_email', 'smtp_enabled' ) == '' ) return;

    	$phpmailer->isSMTP();
    	$phpmailer->Host       = Helper::get_option( 'coschool_email', 'smtp_host' );
    	$phpmailer->Port       = Helper::get_option( 'coschool_email', 'smtp_port' );
    	$phpmailer->SMTPSecure = Helper::get_option( 'coschool_email', 'encryption' );
    	$phpmailer->SMTPAuth   = true;
    	$phpmailer->Username   = Helper::get_option( 'coschool_email', 'smtp_username' );
    	$phpmailer->Password   = Helper::get_option( 'coschool_email', 'smtp_password' );
    	$phpmailer->From       = Helper::get_option( 'coschool_email', 'smtp_from_mail' );
    	$phpmailer->FromName   = Helper::get_option( 'coschool_email', 'smtp_from_name' );
    	$phpmailer->addReplyTo( Helper::get_option( 'coschool_email','smtp_from_mail' ), Helper::get_option( 'coschool_email','smtp_from_name' ) );
	}
}