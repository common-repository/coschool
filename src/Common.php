<?php
/**
 * Common functions that run both on wp-admin and public facing pages
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Common
 * @author Codexpert <hi@codexpert.io>
 */
class Common extends Base {

	public $plugin;

	public $slug;
	
	public $name;

	public $version;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->version	= $this->plugin['Version'];
	}

	public function query_vars( $vars ) {
    	$vars[] = "coschool_dtab";
    	return $vars;
	}

	public function rewrite_rule() {
    	$dashboard_id   = coschool_dashboard_page();
    	$dashboard      = get_post( $dashboard_id );
     
    	if( ! is_object( $dashboard ) ) return;

    	add_rewrite_rule( $dashboard->post_name . '/?([^/]*)/?', 'index.php?pagename=' . $dashboard->post_name . '&coschool_dtab=$matches[1]', 'top' );
	}

	public function user_login( $user_login, $user ) {

		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) { 
			$ip = coschool_sanitize( $_SERVER['HTTP_CLIENT_IP'] ); 
		}
		elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = coschool_sanitize( $_SERVER['HTTP_X_FORWARDED_FOR'] ); 
		}
		else { 
			$ip = coschool_sanitize( $_SERVER['REMOTE_ADDR'] ); 
		}

		$_login_info = get_user_meta( $user->ID, 'login_info', true ) ? : [];

		$_login_info[ time() ] = $ip;

		$login_info = array_slice( $_login_info, -10, null, true );

		update_user_meta( $user->ID, 'login_info', $login_info );
	}
}