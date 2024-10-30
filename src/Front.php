<?php
/**
 * All public facing functions
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;
use Codexpert\CoSchool\App\Quiz\Data as Quiz_Data;
use Codexpert\CoSchool\App\Question\Data as Question_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Front
 * @author Codexpert <hi@codexpert.io>
 */
class Front extends Base {

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

	public function add_admin_bar( $admin_bar ) {
		if( ! current_user_can( 'manage_options' ) ) return;

		$admin_bar->add_menu( [
			'id'    => $this->slug,
			'title' => $this->name,
			'href'  => add_query_arg( 'page', $this->slug, admin_url( 'admin.php' ) ),
			'meta'  => [
				'title' => $this->name,            
			],
		] );
	}

	public function head() {}

	/**
	 * Adds a class to the <body> tag
	 *
	 * @since 1.0
	 */
	public function body_class( $class ) {
		if( is_coschool() ) {
			$class[] = $this->slug;
		}
		return $class;
	}

	public function modal() {
		echo '
		<div id="coschool-modal" style="display: none">
			<img id="coschool-modal-loader" src="'. esc_attr( COSCHOOL_ASSET . '/img/loader.gif' ) .'" />
		</div>';
	}

	public function login_form_top( $html, $args ){
		if ( ! isset( $args['form_id'] ) || 'coschool-login-form' != $args['form_id'] ) return $html;

		return '<fieldset><legend>'. __( 'Login', 'coschool' ) .'</legend>';
	}

	public function login_form_bottom( $html, $args ){
		if ( ! isset( $args['form_id'] ) || 'coschool-login-form' != $args['form_id'] ) return $html;

		return '</fieldset>';
	}
	
	public function avatar_url( $url, $id_or_email, $args ) {

		$avatar = get_user_meta( $id_or_email, '_coschool_avatar', true );
		
		if ( $avatar ) {
			return $avatar;
		}

	    return $url; 
	}
	public function remove_admin_bar() {
		if ( current_user_can( 'student' ) ) {
			show_admin_bar( false );
		}
	}

}