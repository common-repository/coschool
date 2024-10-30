<?php
/**
 * All assignment related functions
 */
namespace Codexpert\CoSchool\App\Course;
use Codexpert\Plugin\Base;
use Codexpert\Plugin\Metabox as Metabox_API;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Course
 * @author Codexpert <hi@codexpert.io>
 */
class Meta {

	/**
	 * Generates settings metabox
	 * 
	 * @uses Codexpert\Plugin\Metabox
	 */
	public function config() {


		$current_user_id 	= get_current_user_id();

		if( ! current_user_can( 'edit_pages' ) ) {
			$certificate_args['author']	= $current_user_id;
		}

		$metabox = [
			'id'			=> 'coschool-course-settings',
			'label'			=> __( 'Course Configuration', 'coschool' ),
			'post_type'		=> 'course',
			'topnav'		=> wp_is_mobile(),
			'sections'		=> [
				'coschool_pricing'	=> [
					'id'        => 'coschool_pricing',
					'label'     => __( 'Pricing', 'coschool' ),
					'icon'      => 'dashicons-money-alt',
					'no_heading'=> true,
					'fields'    => [
						'price_type' 	=> [
							'id'        => 'price_type',
							'label'     => __( 'Course Type', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'Set the type of course', 'coschool' ),
							'options'   => [
								'free'		=> __( 'Free', 'coschool' ),
								'premium'	=> __( 'Premium', 'coschool' ),
							],
							'default'	=> 'premium',
						],
						'price' 		=> [
							'id'        => 'price',
							'label'     => __( 'Price', 'coschool' ),
							'type'      => 'number',
							'min'		=> 0,
							'step'		=> 0.01,
							'desc'      => __( 'Input course price without the currency symbol.', 'coschool' ),
							'condition'	=> [
								'key'	=> 'price_type',
								'value'	=> 'premium'
							]
						],
					]
				],
			]
		];
		
		if( coschool_schema_enabled() ) {
			$metabox[ 'sections' ][ 'coschool_advanced' ] = [
				'id'        => 'coschool_advanced',
				'label'     => __( 'Advanced', 'coschool' ),
				'icon'      => 'dashicons-admin-generic',
				'no_heading'=> true,
				'fields'    => [
					'duration_divider' => [
						'id'		=> 'duration_divider',
						'label'     => __( 'Course Estimated Duration', 'coschool' ),
						'type'      => 'divider',
					],
					'course_duration' => [
						'id'      	=> 'course_duration',
						'label'     => __( 'Course Duration', 'coschool' ),
						'type'      => 'text',
						'desc'      => __( 'Set the duration of current Course.', 'coschool' ),		
						'default'   =>  '1Hour 20Min',				
					],
					'metakey_config' => [
						'id'		=> 'metakey_config',
						'label'     => __( 'Basic SEO', 'coschool' ),
						'type'      => 'divider',
					],
					'meta_keyword' 	=> [
						'id'      	=> 'meta_keyword',
						'label'     => __( 'Meta Keywords', 'coschool' ),
						'type'      => 'text',
					],
					'meta_description' => [
						'id'      	=> 'meta_description',
						'label'     => __( 'Meta Description', 'coschool' ),
						'type'      => 'textarea',
					],
					'schema_config' => [
						'id'      	=> 'schema_config',
						'label'     => __( 'Schema Markup', 'coschool' ),
						'type'      => 'divider',
					],
					'enable_schema' => [
						'id'      	=> 'enable_schema',
						'label'     => __( 'Enable', 'coschool' ),
						'type'      => 'select',
						'desc'      => __( 'Do you want to enable schema markup for the course? Don\'t enable if you have other related plugins activated.', 'coschool' ),
						'options'   => [
							'yes'	=> __( 'Yes', 'coschool' ),
							'no'	=> __( 'No', 'coschool' ),
						],
						'default'   => 'no',
					],
					'schema_rating' => [
						'id'        => 'schema_rating',
						'label'     => __( 'Aggregate Rating', 'coschool' ),
						'type'      => 'select',
						'desc'      => __( 'Do you want to show aggregate rating in SERPs?', 'coschool' ),
						'options'   => [
							'no'		=> __( 'Disable', 'coschool' ),
							'real'		=> __( 'Use real values', 'coschool' ),
							'custom'	=> __( 'Input custom values', 'coschool' ),
						],
						'default'   => 'no',
						'condition'	=> [
							'key'	=> 'enable_schema',
							'value'	=> 'yes'
						]
					],
					'schema_rating_args' => [
						'id'        => 'schema_rating_args',
						'label'     => __( 'Rating Values', 'coschool' ),
						'type'      => 'group',
						'items'		=> [
							'ratingValue' => [
								'id'        	=> 'ratingValue',
								'type'      	=> 'number',
								'placeholder'	=> __( 'Rating Value', 'coschool' ),
								'step'			=> 0.01,
							],
							'bestRating' => [
								'id'        	=> 'bestRating',
								'type'      	=> 'number',
								'placeholder'	=> __( 'Best Rating', 'coschool' ),
								'step'			=> 0.01,
							],
							'worstRating' => [
								'id'        	=> 'worstRating',
								'type'      	=> 'number',
								'placeholder'	=> __( 'Worst Rating', 'coschool' ),
								'step'			=> 0.01,
							],
							'ratingCount' => [
								'id'        	=> 'ratingCount',
								'type'      	=> 'number',
								'placeholder'	=> __( 'Rating Count', 'coschool' ),
								'step'			=> 0.01,
							],
						],
						'condition'	=> [
							'key'	=> 'schema_rating',
							'value'	=> 'custom'
						]
					],
				]
			];
		}

		$metabox = apply_filters( 'coschool_course_metabox', $metabox, $this );

		new Metabox_API( $metabox );
	}

	/**
	 * Generates content metabox
	 * 
	 * @uses add_meta_box()
	 */
	public function content() {
		add_meta_box( 'coschool-course-content', __( 'Course Content', 'coschool' ), [ $this, 'callback_content' ], 'course', 'normal', 'high' );
		add_meta_box( 'coschool-course-faq', __( 'Course FAQ', 'coschool' ), [ $this, 'callback_faq' ], 'course', 'normal', 'high' );
		add_meta_box( 'coschool-banner-image', __( 'Banner Image', 'coschool' ), [ $this, 'callback_banner_image' ], 'course', 'side', 'low' );
	}

	public function callback_content() {
		echo Helper::get_view( 'content', 'views/metabox/course' );
	}

	public function callback_faq() {
		echo Helper::get_view( 'faq', 'views/metabox/course' );
	}

	public function callback_banner_image() {
		echo Helper::get_view( 'banner-image', 'views/metabox/course' );
	}

	public function save( $course_id, $course ) {

		$course_data = new Data( $course_id );

		// remove old meta from all content
		global $wpdb;
		
		$wpdb->delete( $wpdb->postmeta, [ 'meta_key' => 'course_id', 'meta_value' => $course_id ] );
		
		// update course contents
		if( isset( $_POST['course_contents'] ) ) :
			$new_contents	= $_POST['course_contents'];
			$course_data->set( 'course_contents', coschool_sanitize( $new_contents, 'array' ) );

			// update new meta to all content
			if( ! empty( $new_contents ) ) :
			foreach ( $new_contents as $chapter => $contents ) {
				foreach ( $contents as $content_id ) {
					update_post_meta( $content_id, 'course_id', $course_id ); // @TODO replace with `Data` object or an SQL query
				}
			}
			endif;
		endif;
		
		// update course contents
		if( isset( $_POST['course_faq'] ) ) :
			$new_faqs	= coschool_sanitize( $_POST['course_faq'], 'array' );

			$course_data->set( 'course_faq', $new_faqs );

			unset( $new_faqs['count'] );

			if( ! empty( $new_faqs ) ) :
				update_post_meta( $course_id, 'course_faq', $new_faqs );
			endif;
		endif;

		// update certificate
		if( isset( $_POST['coschool_certification'] ) ) {
			$certification = coschool_sanitize( $_POST['coschool_certification'], 'array' );
			if( isset( $certification['enable_certificate'] ) && 'yes' == $certification['enable_certificate'] && isset( $certification['certificate'] ) && '' != $certification['certificate'] ) {
				update_post_meta( $certification['certificate'] , 'course_id', $course_id );
			}
		}

		// update user cap
		if( $course_data->get( 'instructor' ) == get_current_user_id() ) {
			$instructor = new \WP_User( $course_data->get( 'instructor' ) );
			if( ! $instructor->has_cap( 'create_courses' ) ) {
				$instructor->add_cap( 'create_courses' );
			}
		}

		// banner image
		if ( isset( $_POST['course_banner'] ) ) {
			$course_data->set( 'course_banner', coschool_sanitize( $_POST['course_banner'] ) );
		}

		do_action( 'coschool_course_saved', $course_data );
	}

	public function show_schema() {

		global $post;
		
		if( ! is_a( $post, 'WP_Post' ) || $post->post_type != 'course' ) return;

		$course_data = new Data( $post->ID );

		$coschool_advanced = $course_data->get( 'coschool_advanced' );

		$excerpt = get_the_excerpt( $course_data->get( 'id' ) );

		if( isset( $coschool_advanced['meta_keyword']) && $coschool_advanced['meta_keyword'] != '' ) {
			$keywords = coschool_sanitize( $coschool_advanced['meta_keyword'] );
			echo "<meta name='keywords' content='" . esc_attr( $keywords ) . "' />";
		}

		if( isset( $coschool_advanced['meta_description']) && $coschool_advanced['meta_description'] != '' ) {
			$excerpt = coschool_sanitize( $coschool_advanced['meta_description'] );
			echo "<meta name='description' content='" . esc_attr( $excerpt ) . "' />";
		}

		if( isset( $coschool_advanced['enable_schema'] ) && 'yes' != $coschool_advanced['enable_schema'] ) return;

		$schema_args = [
			'@context'			=> 'https://schema.org/',
			'@type'				=> 'Course',
			'courseCode'		=> 'CRS' . $course_data->get( 'id' ),
			'name'				=> $course_data->get( 'name' ),
			'description'		=> $excerpt,
			'provider'			=> [
				'@type'	=> 'CollegeOrUniversity',
				'name'	=> get_bloginfo( 'name' ),
				'url'	=> [
					'@id'	=> coschool_site_url(),
				],
			],
			'offers'			=> [
			  '@type'			=> 'Offer',
			  'url'				=> get_permalink( $course_data->get( 'id' ) ),
			  'priceCurrency'	=> coschool_get_currency( 'code' ),
			  'price'			=> $course_data->get_price(),
			],
		];

		if( isset( $coschool_advanced['schema_rating'] ) && 'real' == $coschool_advanced['schema_rating'] ) {
			$schema_args['aggregateRating']	= [
				'@type'			=> 'AggregateRating',
				'ratingValue'	=> $course_data->get_rating(),
				'bestRating'	=> 5,
				'worstRating'	=> 1,
				'ratingCount'	=> count( $course_data->get_reviews() ),
			];
		}

		if( isset( $coschool_advanced['schema_rating'] ) && 'custom' == $coschool_advanced['schema_rating'] ) {
			$schema_args['aggregateRating']	= [
				'@type'			=> 'AggregateRating',
				'ratingValue'	=> coschool_sanitize( $coschool_advanced['ratingValue'] ),
				'bestRating'	=> coschool_sanitize( $coschool_advanced['bestRating'] ),
				'worstRating'	=> coschool_sanitize( $coschool_advanced['worstRating'] ),
				'ratingCount'	=> coschool_sanitize( $coschool_advanced['ratingCount'] ),
			];
		}

		echo "<script type='application/ld+json'>" . json_encode( $schema_args, JSON_PRETTY_PRINT ) . "</script>";
	}
}