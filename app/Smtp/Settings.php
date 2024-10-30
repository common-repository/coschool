<?php
namespace Codexpert\CoSchool\App\Smtp;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	public function smtp_settings( $settings ){
		$smtp =  [	'smtp_heading'	=> [
							'id'		=> 'smtp_heading',
							'label'     => __( 'SMTP', 'coschool' ),
							'type'      => 'divider',
						],
						'smtp_enabled' => [
							'id'        => 'smtp_enabled',
							'label'     => __( 'Enable Custom SMTP', 'coschool' ),
							'desc'		=> __( 'Enable this if you want to use custom SMTP.', 'coschool' ),
							'type'      => 'switch',
						],
						'smtp_host' => [
							'id'        => 'smtp_host',
							'label'     => __( 'SMTP Host Name', 'coschool' ),
							'type'      => 'text',
							'desc'      => __( 'Input SMTP Host Name.', 'coschool' ),
							'required'	=> true,
							'condition'	=> [
								'key'		=> 'smtp_enabled',
								'compare'	=> 'checked'
							],
						],
						'smtp_port' => [
							'id'        => 'smtp_port',
							'label'     => __( 'SMTP Port', 'coschool' ),
							'type'      => 'number',
							'desc'      => __( 'Input SMTP Port.', 'coschool' ),
							'required'	=> true,
							'condition'	=> [
								'key'		=> 'smtp_enabled',
								'compare'	=> 'checked'
							],
						],
						'encryption' => [
							'id'        => 'encryption',
							'label'     => __( 'Encryption', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'Select Encryption?', 'coschool' ),
							'options'	=> [ 'tls' => __( 'TLS', 'coschool' ), 'ssl' => __( 'SSL', 'coschool' ), 'none' => __( 'None', 'coschool' ) ],
							'default'	=> 'tls',
							'chosen'	=> true,
							'condition'	=> [
								'key'		=> 'smtp_enabled',
								'compare'	=> 'checked'
							],
						],
						'smtp_username' => [
							'id'        => 'smtp_username',
							'label'     => __( 'SMTP Usernmae', 'coschool' ),
							'type'      => 'text',
							'desc'      => __( 'Input SMTP Username.', 'coschool' ),
							'required'	=> true,
							'condition'	=> [
								'key'		=> 'smtp_enabled',
								'compare'	=> 'checked'
							],
						],
						'smtp_password' => [
							'id'        => 'smtp_password',
							'label'     => __( 'SMTP password', 'coschool' ),
							'type'      => 'password',
							'desc'      => __( 'Input SMTP password.', 'coschool' ),
							'required'	=> true,
							'condition'	=> [
								'key'		=> 'smtp_enabled',
								'compare'	=> 'checked'
							],
						],
						'smtp_from_mail' => [
							'id'        => 'smtp_from_mail',
							'label'     => __( 'From Email', 'coschool' ),
							'type'      => 'email',
							'desc'      => __( 'Input from email.', 'coschool' ),
							'condition'	=> [
								'key'		=> 'smtp_enabled',
								'compare'	=> 'checked'
							],
						],
						'smtp_from_name' => [
							'id'        => 'smtp_from_name',
							'label'     => __( 'From Name', 'coschool' ),
							'type'      => 'text',
							'desc'      => __( 'Input from name.', 'coschool' ),
							'condition'	=> [
								'key'		=> 'smtp_enabled',
								'compare'	=> 'checked'
							],
						],
					];
	
		$settings['sections']['coschool_email']['fields'] = array_merge( $settings['sections']['coschool_email']['fields'], $smtp );
		
		return $settings;
	}
}