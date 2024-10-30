<?php
/**
 * AJAX for the reports
 */
namespace Codexpert\CoSchool\App\Report;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Report
 * @author Codexpert <hi@codexpert.io>
 */
class AJAX {

	public function get_filter_items() {

        $response = [ 'status' => 0, 'message' => __( 'Unauthorized', 'coschool' ) ];
        
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'coschool' ) ) {
            wp_send_json( $response );
        }

        $items = [];
        switch ( coschool_sanitize( $_POST['by'] ) ) {
            case 'course':

                $args = [ 'post_type' => 'course' ];

                if( ! current_user_can( 'edit_pages' ) ) {
                    $args['author'] = get_current_user_id();
                }

                $items = Helper::get_posts( $args );
                break;
            
            case 'category':
                $items = Helper::get_terms( [ 'taxonomy' => 'course-category' ] );
                break;
            
            case 'instructor':
                $items = Helper::get_users( [ 'capability__in' => [ 'create_courses' ] ] );
                break;
        }

        wp_send_json( [ 'status' => 1, 'message' => __( 'Success', 'coschool' ), 'items' => $items ] );
    }
	
}