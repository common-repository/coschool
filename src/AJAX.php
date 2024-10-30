<?php
/**
 * All AJAX related functions
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
 * @subpackage AJAX
 * @author Codexpert <hi@codexpert.io>
 */
class AJAX extends Base {

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

	public function user_switcher() {
		$response = [];

        if( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
            $response['status']		= 0;
            $response['message'] 	= __( 'Unauthorized!', 'coschool' );
            wp_send_json( $response );
        }

        $user_type = ! in_array( ( $user_type = get_user_meta( get_current_user_id(), '_viewing_as', true ) ), [ 'instructor', 'student' ] ) ? 'instructor' : $user_type;

        $new_type = $user_type == 'instructor' ? 'student' : 'instructor';

        update_user_meta( get_current_user_id(), '_viewing_as', $new_type );
	}
}