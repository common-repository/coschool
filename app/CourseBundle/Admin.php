<?php
namespace Codexpert\CoSchool\App\CourseBundle;
use Codexpert\CoSchool\App\CourseBundle\Data;
use Codexpert\CoSchool\Helper;
use Codexpert\Plugin\Metabox as Metabox_API;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Admin
 * @author Codexpert <hi@codexpert.io>
 */
class Admin {
	
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
		
		wp_enqueue_style( $this->slug, plugins_url( "/app/CourseBundle/assets/css/admin.css", COSCHOOL ), '', $this->version, 'all' );

		wp_enqueue_script( $this->slug, plugins_url( "/app/CourseBundle/assets/js/admin.js", COSCHOOL ), [ 'jquery' ], $this->version, true );
	}

	/**
	 * Generates settings metabox
	 * 
	 * @uses Codexpert\Plugin\Metabox
	 */
	public function config() {
		$course_args = [
			'post_type'	=> 'course',
		];

		if( ! current_user_can( 'edit_pages' ) ) {
			$course_args['author']	= get_current_user_id();
		}

		$metabox = [
			'id'			=> 'coschool-bundle-settings',
			'label'			=> __( 'Bundle Configuration', 'coschool' ),
			'post_type'		=> 'bundle',
			'topnav'		=> wp_is_mobile(),
			'sections'		=> [
				'bundle_pricing'	=> [
					'id'        => 'bundle_pricing',
					'label'     => __( 'bundle', 'coschool' ),
					'icon'      => 'dashicons-money-alt',
					'no_heading'=> true,
					'fields'    => [
						'select_course'	=> [
							'id'		=> 'select_course',
							'label'     => __( 'Select Courses', 'coschool' ),
							'type'      => 'select',
							'multiple'  => true,
							'chosen'      => true,
							'options'	=> Helper::get_posts( $course_args ),
						],
						'price' => [
							'id'        => 'price',
							'label'     => __( 'Price', 'coschool' ),
							'type'      => 'number',
							'min'		=> 0,
							'step'		=> 0.01,
							'desc'      => __( 'Input price without the currency symbol.', 'coschool' ),
						],
						
					]
				],
			]
		];
		
		$metabox = apply_filters( 'coschool_bundle_metabox', $metabox, $this );

		new Metabox_API( $metabox );
	}

	public function save( $bundle_id, $bundle ) {

		$bundle_data = new Data( $bundle_id );
		
		do_action( 'coschool_bundle_saved', $bundle_data );
	}

	/**
	* Bundle updated text
	* 
 	* @param string mesages
	*/
	public function bundle_updated_message( $messages ){
		
		$messages['bundle'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Bundle updated. ', 'coschool') ,
			6  => __( 'Bundle created. ', 'coschool') ,
		);

		return $messages;
	}

	/**
	* Bulk Bundle delete text
	* 
 	* @param string mesages
	*/
	public function bulk_bundle_updated_message( $bulk_messages, $bulk_counts ){
		
	    $bulk_messages['bundle'] = array(
	        'updated'   => _n( '%s Bundle updated.', '%s Bundles updated.', $bulk_counts['updated'], 'coschool' ),
	        'locked'    => _n( '%s Bundle not updated, somebody is editing it.', '%s Bundles not updated, somebody is editing them.', $bulk_counts['locked'], 'coschool' ),
	        'deleted'   => _n( '%s Bundle permanently deleted.', '%s Bundles permanently deleted.', $bulk_counts['deleted'], 'coschool' ),
	        'trashed'   => _n( '%s Bundle moved to the Trash.', '%s Bundles moved to the Trash.', $bulk_counts['trashed'], 'coschool' ),
	        'untrashed' => _n( '%s Bundle restored from the Trash.', '%s Bundles restored from the Trash.', $bulk_counts['untrashed'], 'coschool' ),
	    );
	 
	    return $bulk_messages;
	}
}