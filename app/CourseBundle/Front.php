<?php
namespace Codexpert\CoSchool\App\CourseBundle;

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
class Front {

	public $slug;

	public $version;

	/**
	 * Constructor function
	 */
	public function __construct() {
        $this->slug     = 'course-bundle';
        $this->version  = time();
    }
    
	/**
	 * Enqueue JavaScripts and stylesheets
	 */
	public function enqueue_scripts() {
		$min = defined( 'COSCHOOL_DEBUG' ) && COSCHOOL_DEBUG ? '' : '.min';

		wp_enqueue_style( $this->slug, plugins_url( "modules/CourseBundle/assets/css/front{$min}.css", COSCHOOL ), '', $this->version, 'all' );
		
		// $localized = [
		// 	'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
		// 	'_wpnonce'	=> wp_create_nonce(),
		// ];
		// wp_localize_script( $this->slug, 'COSCHOOL', apply_filters( "{$this->slug}-localized", $localized ) );
	}
}