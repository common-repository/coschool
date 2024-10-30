<?php
namespace Codexpert\CoSchool\App;

class CourseBundle {
    	
	/**
	 * Plugin instance
	 * 
	 * @access private
	 * 
	 * @var Plugin
	 */
	private static $_instance;

    public function __construct() {

		if( is_admin() ) :

			/**
			 * Admin facing hooks
			 */
			add_action( 'admin_enqueue_scripts', [ new CourseBundle\Admin, 'enqueue_scripts' ] );
			add_action( 'admin_init', [ new CourseBundle\Admin, 'config' ], 11 );
			add_action( 'save_post_bundle', [ new CourseBundle\Admin, 'save' ], 10, 2 );
			add_filter( 'post_updated_messages', [ new CourseBundle\Admin, 'bundle_updated_message' ] );
			add_filter( 'bulk_post_updated_messages', [ new CourseBundle\Admin, 'bulk_bundle_updated_message' ], 10, 2 );

        else :
            /**
			 * Front facing hooks
			 */
			add_action( 'wp_enqueue_scripts', [ new CourseBundle\Front, 'enqueue_scripts' ] );

        endif;

			/**
			 * Common hooks
			 */
			add_action( 'init', [ new CourseBundle\Common, 'register_cpt' ], 11 );
			add_action( 'manage_bundle_posts_columns', [ new CourseBundle\Common, 'add_table_columns' ] );
			add_action( 'manage_bundle_posts_custom_column', [ new CourseBundle\Common, 'add_column_content' ], 10, 2 );
    }

	/**
	 * Instantiate the plugin
	 * 
	 * @access public
	 * 
	 * @return $_instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}