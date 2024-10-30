<?php
/**
 * Common assets handler
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Certificate\Data as Certificate_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage App
 * @author Codexpert <hi@codexpert.io>
 */
class Assets extends Base {

	public $plugin;

	public $slug;
	
	public $name;

	public $server;

	public $version;

	public $admin_css;

	public $admin_js;

	public $front_css;

	public $front_js;

	/**
	 * Either we're going to use the minified version of CSS and JS files
	 * 
	 * @var string
	 */
	private $min;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];

		$this->admin_css	= $this->plugin['assets'] . '/admin/css';
		$this->admin_js		= $this->plugin['assets'] . '/admin/js';
		$this->front_css	= $this->plugin['assets'] . '/front/css';
		$this->front_js		= $this->plugin['assets'] . '/front/js';

		$this->min	= defined( 'COSCHOOL_DEBUG' ) && COSCHOOL_DEBUG ? '' : '.min';
	}

	/**
	 * Enqueue admin stylesheets
	 */
	public function admin_css() {
		wp_enqueue_style( $this->slug . '-admin-general', "{$this->admin_css}/general{$this->min}.css", '', $this->version, 'all' );

		wp_register_style( $this->slug . '-admin-course', "{$this->admin_css}/course{$this->min}.css", '', $this->version, 'all' );

		wp_register_style( $this->slug . '-admin-quiz', "{$this->admin_css}/quiz{$this->min}.css", '', $this->version, 'all' );
		
		wp_enqueue_style( $this->slug . '-add-ons', "{$this->admin_css}/add-ons{$this->min}.css", '', $this->version, 'all' );
		wp_enqueue_style( $this->slug . '-assignment', "{$this->front_css}/assignment/admin{$this->min}.css", '', $this->version, 'all' );
		
	}

	/**
	 * Enqueue admin JavaScripts
	 */
	public function admin_js() {	
		wp_enqueue_script( $this->slug . '-admin-general', "{$this->admin_js}/general{$this->min}.js", [ 'jquery' ], $this->version, true );

		wp_register_script( $this->slug . '-admin-course', "{$this->admin_js}/course{$this->min}.js", [ 'jquery' ], $this->version, true );
		wp_register_script( $this->slug . '-admin-quiz', "{$this->admin_js}/quiz{$this->min}.js", [ 'jquery' ], $this->version, true );

		wp_enqueue_script( 'chart.js', 'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js', [], '3.7.1', true );
		wp_enqueue_script( $this->slug . '-admin-reports', "{$this->admin_js}/reports{$this->min}.js", [ 'jquery' ], $this->version, true );

		$localized = [
			'ajaxurl'		=> admin_url( 'admin-ajax.php' ),
			'_wpnonce'		=> wp_create_nonce( 'coschool' ),
			'site_url'		=> coschool_site_url(),
			'edit_base'		=> admin_url( 'post.php?action=edit&post=' ),
			'add_content'	=> __( 'Add Content', 'coschool' ),
			'add_chapter'	=> __( 'Add Chapter', 'coschool' ),
			'chapter_name'	=> __( 'Chapter Name', 'coschool' ),
		];

		wp_localize_script( $this->slug . '-admin-general', 'COSCHOOL', apply_filters( "{$this->slug}-admin-localized", $localized ) );
	}

	/**
	 * Enqueue front fonts
	 */
	public function front_fonts() {
		wp_enqueue_style( 'fontawesome', 'https://pro.fontawesome.com/releases/v5.10.0/css/all.css' );
	}

	/**
	 * Enqueue front stylesheets
	 */
	public function front_css() {
		global $post;
		wp_enqueue_style( 'dashicons' );
		
		wp_enqueue_style( $this->slug . '-front-general', "{$this->front_css}/general{$this->min}.css", '', $this->version, 'all' );
		wp_enqueue_style( $this->slug . '-authentication', "{$this->front_css}/authentication{$this->min}.css", '', $this->version, 'all' );
		
		
		if ( is_singular( 'lesson' ) ) {
			wp_enqueue_style( $this->slug . '-lesson', "{$this->front_css}/lesson{$this->min}.css", '', $this->version, 'all' );
		}
		
		if( is_singular( 'course' ) ) {
			wp_enqueue_style( $this->slug . '-course-single', "{$this->front_css}/course-single{$this->min}.css", '', $this->version, 'all' );
		}
		
		if ( is_archive( 'course' ) ) {
			wp_enqueue_style( $this->slug . '-course-archive', "{$this->front_css}/course-archive{$this->min}.css", '', $this->version, 'all' );
		}
		
		if( is_singular( 'quiz' ) ) {
			wp_enqueue_style( $this->slug . '-quiz', "{$this->front_css}/quiz{$this->min}.css", '', $this->version, 'all' );
		}
		
		if( is_page( coschool_dashboard_page() ) ){
			wp_enqueue_style( $this->slug . '-dashboard', "{$this->front_css}/dashboard{$this->min}.css", '', $this->version, 'all' );
		}
		
		if( is_singular( 'assignment' ) ) {
			wp_enqueue_style( $this->slug . '-assignment', "{$this->front_css}/assignment/front{$this->min}.css", '', $this->version, 'all' );
		}
		
		if( is_page( coschool_enroll_page() ) ) {
			wp_enqueue_style( $this->slug . '-cart', "{$this->front_css}/cart{$this->min}.css", '', $this->version, 'all' );
		}

		//theme compatibality
		
		if( function_exists( 'et_divi_enqueue_stylesheet' ) ) {
			wp_enqueue_style( $this->slug . '-divi', "{$this->front_css}/theme/divi{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'avada_render_blog_post_content' ) ) {
			wp_enqueue_style( $this->slug . '-avada', "{$this->front_css}/theme/avada{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'mfn_header_style' ) ) {
			wp_enqueue_style( $this->slug . '-betheme', "{$this->front_css}/theme/betheme{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'hello_elementor_setup' ) ) {
			wp_enqueue_style( $this->slug . '-hello-elementor', "{$this->front_css}/theme/hello-elementor{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'oceanwp_schema_markup' )) {
			wp_enqueue_style( $this->slug . '-ocean-wp', "{$this->front_css}/theme/ocean-wp{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'neve_filter_sdk' ) ){
			wp_enqueue_style( $this->slug . '-neve', "{$this->front_css}/theme/neve{$this->min}.css", '', $this->version, 'all' );
		}

		$theme_name = wp_get_theme()->name;
		
		if( $theme_name == 'Kadence' ){
			wp_enqueue_style( $this->slug . '-kadence', "{$this->front_css}/theme/kadence{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'generate_setup' ) ){
			wp_enqueue_style( $this->slug . '-generate-press', "{$this->front_css}/theme/generate-press{$this->min}.css", '', $this->version, 'all' );
		}
		
		if( function_exists( 'storefront_primary_navigation' ) ){
			wp_enqueue_style( $this->slug . '-store-front', "{$this->front_css}/theme/store-front{$this->min}.css", '', $this->version, 'all' );
		}
		
		if( function_exists( 'blocksy_before_current_template' ) ){
			wp_enqueue_style( $this->slug . '-blocksy', "{$this->front_css}/theme/blocksy{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'twentytwentytwo_styles' ) ){
			wp_enqueue_style( $this->slug . '-twentytwentytwo', "{$this->front_css}/theme/twentytwentytwo{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'twentytwentyone_the_html_classes' ) ){
			wp_enqueue_style( $this->slug . '-twentytwentyone', "{$this->front_css}/theme/twentytwentyone{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'twentytwenty_register_scripts' ) ){
			wp_enqueue_style( $this->slug . '-twentytwenty', "{$this->front_css}/theme/twentytwenty{$this->min}.css", '', $this->version, 'all' );
		}

		if( function_exists( 'twentynineteen_scripts' ) ){
			wp_enqueue_style( $this->slug . '-twentynineteen', "{$this->front_css}/theme/twentynineteen{$this->min}.css", '', $this->version, 'all' );
		}

		
	}

	/**
	 * Enqueue admin JavaScripts
	 */
	public function front_js() {
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_media();

		wp_enqueue_script( $this->slug . '-front-general', "{$this->front_js}/general{$this->min}.js", [ 'jquery' ], $this->version, true );
		wp_enqueue_script( $this->slug . '-front-course', "{$this->front_js}/course{$this->min}.js", [ 'jquery' ], $this->version, true );
		wp_enqueue_script( $this->slug . '-authentication', "{$this->front_js}/authentication{$this->min}.js", [ 'jquery' ], $this->version, true );
		wp_enqueue_script( $this->slug . '-dashboard', "{$this->front_js}/dashboard{$this->min}.js", [ 'jquery' ], $this->version, true );
		wp_enqueue_script( $this->slug . '-dashboard-quiz', "{$this->front_js}/dashboard-quiz{$this->min}.js", [ 'jquery' ], $this->version, true );
		wp_enqueue_script( $this->slug . '-assignment', "{$this->front_js}/assignment{$this->min}.js", [ 'jquery' ], $this->version, true );
		
		if( is_page( coschool_dashboard_page() ) ) {
			wp_enqueue_script( 'chart.js', 'https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js', [], '3.7.1', true );
			wp_enqueue_script( $this->slug . '-front-admin-reports', "{$this->front_js}/admin-reports{$this->min}.js", [ 'jquery' ], $this->version, true );
			wp_enqueue_script( $this->slug . '-front-student-reports', "{$this->front_js}/student-reports{$this->min}.js", [ 'jquery' ], $this->version, true );
		}

		$localized = [
			'ajaxurl'			=> admin_url( 'admin-ajax.php' ),
            'nonce' 			=> wp_create_nonce( 'coschool' ),
            'checkout_url'		=> get_permalink( Helper::get_option( 'coschool_general','enroll_page' ) ),
            'pass1_error' 		=> __( 'Password must have 8 characters', 'coschool' ),
            'pass2_error' 		=> __( 'Password not matched', 'coschool' ),
			'dashboard_page' 	=> coschool_dashboard_page( true ),
			'confirm_message' 	=> __( 'Are you sure you want to delete this?', 'coschool' ),
		];
		wp_localize_script( $this->slug . '-front-course', 'COSCHOOL', apply_filters( "{$this->slug}-localized-quiz", $localized ) );


		if( is_singular( 'quiz' ) ) {
			global $quiz_data;
			
			$time_config 	= $quiz_data->get_config( 'coschool_quiz_config_time' );

			$quiz_time = 0;
			if ( isset( $time_config['enable_quiz_time'] ) ) {
				$quiz_time += isset( $time_config['quiz_time_h'] ) && '' != $time_config['quiz_time_h'] ? HOUR_IN_SECONDS * $time_config['quiz_time_h'] : 0 ;
				$quiz_time += isset( $time_config['quiz_time_m'] ) && '' != $time_config['quiz_time_m'] ? MINUTE_IN_SECONDS * $time_config['quiz_time_m'] : 0 ;
				$quiz_time += isset( $time_config['quiz_time_s'] ) && '' != $time_config['quiz_time_s'] ? $time_config['quiz_time_s'] : 0 ;
			}
			$localized = [
				'quiz_time'	=> $quiz_time,
				'time_up'	=> $quiz_time ? __( 'Time Up', 'coschool' ) : '00:00:00',
			];
			
			wp_enqueue_script( $this->slug . '-quiz', "{$this->front_js}/quiz{$this->min}.js", [ 'jquery' ], $this->version, true );
			wp_localize_script( $this->slug . '-quiz', 'COSCHOOL_QUIZ', apply_filters( "{$this->slug}-localized-quiz", $localized ) );
		}

		if ( is_singular( 'lesson' ) ) {
			wp_enqueue_script( $this->slug . '-lesson', "{$this->front_js}/lesson{$this->min}.js", [ 'jquery' ], $this->version, true );
		}

		if( is_page( coschool_enroll_page() ) ) {
			wp_enqueue_script( $this->slug . '-cart', "{$this->front_js}/cart{$this->min}.js", '', $this->version, false );
		}

		if ( is_singular( 'assignment' ) ) {
			wp_enqueue_script( $this->slug . '-assignment', "{$this->front_js}/assignment{$this->min}.js", [ 'jquery' ], $this->version, true );
			$localized 		= [
				'ajaxurl'	=> admin_url( 'admin-ajax.php' ),
	            'nonce' 	=> wp_create_nonce( 'coschool' ),
			];
			wp_localize_script( $this->slug . '-assignment', 'COSCHOOL', apply_filters( "{$this->slug}-localized-assignment", $localized ) );
		}
	}

	public function register_thumbnails() {
		add_image_size( 'coschool-small-thumb', 320, 200, true );
		add_image_size( 'coschool-thumb', 640, 400, true );
		add_image_size( 'coschool-banner', 1125, 563, true );
	}

	public function show_current_user_attachments( $query = [] )	{
		if( ! current_user_can( 'edit_pages' ) ) {
	        $query['author'] = get_current_user_id();
	    }
	    return $query;
	}
}