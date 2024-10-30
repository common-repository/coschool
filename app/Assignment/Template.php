<?php
/**
 * All template facing functions
 */
namespace Codexpert\CoSchool\App\Assignment;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Template
 * @author Codexpert <hi@codexpert.io>
 */
class Template {

	/**
	 * Constructor function
	 */
	// public function __construct() {
    //     $this->init();
    // }

    // public function init() {
        
    //     /**
    //      * Overwrite the template files
    //      * 
    //      * @since 0.9
    //      */

    //     // single template
    //     add_filter( 'single_template', [ $this, 'single_template' ] );

    //     // single assignment
    //     add_action( 'coschool_single_assignment', [ $this, 'content_single_assignment' ] );
    // }

    // public function single_template( $_template ) {
    //     global $post;
        
    //     if ( is_singular( 'assignment' ) && get_post_type( $post ) == 'assignment' && false !== ( $template = Helper::locate_module_template( 'Assignment', 'single-assignment.php' ) ) ) {
    //         return $template;
    //     }
        
    //     return $_template;
    // }

    // public function content_single_assignment() {
    //     if ( false !== ( $template = Helper::locate_module_template( 'Assignment', 'content-single-assignment.php' ) ) ) {
    //         include $template;
    //     }
    // }
}