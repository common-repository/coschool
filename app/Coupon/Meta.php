<?php
/**
 * All coupon related functions
 */
namespace Codexpert\CoSchool\App\Coupon;
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
 * @subpackage Coupon
 * @author Codexpert <hi@codexpert.io>
 */
class Meta {

	/**
	 * Generates settings metabox
	 * 
	 * @uses Codexpert\Plugin\Metabox
	 */
	public function config() {

		$metabox = [
			'id'			=> 'coschool-coupon-settings',
			'label'			=> __( 'Settings', 'coschool' ),
			'post_type'		=> 'coupon',
			'topnav'		=> wp_is_mobile(),
			'sections'		=> [
				'coschool_discount'	=> [
					'id'        => 'coschool_discount',
					'label'     => __( 'Discount', 'coschool' ),
					'icon'      => 'dashicons-admin-tools',
					'no_heading'=> true,
					'fields'    => [
						'enable'		=> [
							'id'        => 'enable',
							'label'     => __( 'Enable', 'coschool' ),
							'type'      => 'switch',
							'desc'      => __( 'Turn this OFF if you want to keep this coupon disabled.', 'coschool' ),
						],
						'discount_type' => [
							'id'        => 'discount_type',
							'label'     => __( 'Discount Type', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'Choose a discount type', 'coschool' ),
							'options'   => [
								'fixed'		=> __( 'Fixed', 'coschool' ),
								'percent'	=> __( 'Percentage', 'coschool' ),
							],
							'default'	=> 'percent',
						],
						'amount' => [
							'id'        	=> 'amount',
							'label'     	=> __( 'Amount', 'coschool' ),
							'type'      	=> 'number',
							'min'			=> 0.01,
							'max'			=> 99999,
							'step'			=> 0.01,
							'placeholder'	=> __( 'Example: 20', 'coschool' ),
							'desc'      	=> __( 'Input the amount only. If you select \'Discount Type\' as \'Percentage\', it\'ll calculate accordingly.', 'coschool' ),
						],
					]
				],
				'coschool_condition'	=> [
					'id'        => 'coschool_condition',
					'label'     => __( 'Conditions', 'coschool' ),
					'icon'      => 'dashicons-text-page',
					'no_heading'=> true,
					'fields'    => [
						'filter_by' 	=> [
							'id'		=> 'filter_by',
							'label'     => __( 'Filter Courses', 'coschool' ),
							'type'      => 'select',
							'desc'      => __( 'Choose courses this coupon applies to.', 'coschool' ),
							'options'   => [
								'all-courses'	=> __( 'All Courses', 'coschool' ),
								'courses'		=> __( 'By Courses', 'coschool' ),
								'instructors'	=> __( 'By Instructors', 'coschool' ),
								'categories'	=> __( 'By Categories', 'coschool' ),
							],
							'chosen'	=> true,
						],
						'courses'		=> [
							'id'		=> 'courses',
							'label'     => __( 'Courses', 'coschool' ),
							'type'		=> 'select',
							'desc'		=> __( 'Choose courses this coupon applies to.' ),
							'options'	=> Helper::get_posts( [ 'post_type' => 'course' ] ),
							'condition'	=> [
								'key'		=> 'filter_by',
								'value'		=> 'courses',
							],
							'multiple'	=> true,
							'chosen'	=> true,
						],
						'instructors'	=> [
							'id'		=> 'instructors',
							'label'     => __( 'Instructors', 'coschool' ),
							'type'		=> 'select',
							'desc'		=> __( 'Choose instructors this coupon applies to.' ),
							'options'	=> Helper::get_users( [ 'capability__in' => 'create_courses' ] ),
							'condition'	=> [
								'key'		=> 'filter_by',
								'value'		=> 'instructors',
							],
							'multiple'	=> true,
							'chosen'	=> true,
						],
						'categories'	=> [
							'id'		=> 'categories',
							'label'     => __( 'Categories', 'coschool' ),
							'type'		=> 'select',
							'desc'		=> __( 'Choose categories this coupon applies to.' ),
							'options'	=> Helper::get_terms( [ 'taxonomy' => 'course-category' ] ),
							'condition'	=> [
								'key'		=> 'filter_by',
								'value'		=> 'categories',
							],
							'multiple'	=> true,
							'chosen'	=> true,
						],
						'has_validity'	=> [
							'id'        => 'has_validity',
							'label'     => __( 'Has Validity?', 'coschool' ),
							'type'      => 'switch',
							'desc'		=> __( 'Does this coupon have any validity period?' ),
						],
						'validity_period'	=> [
							'id'		=> 'validity_period',
							'label'     => __( 'Validity Period', 'coschool' ),
							'type'		=> 'group',
							'desc'		=> __( 'The \'From\' and \'To\' dates of validity period.' ),
							'condition'	=> [
								'key'		=> 'has_validity',
								'compare'	=> 'checked',
							],
							'items'		=> [
								'valid_from'	=> [
									'id'        => 'valid_from',
									'label'		=> __( 'Valid From', 'coschool' ),
									'type'      => 'date',
								],
								'valid_to'	=> [
									'id'        => 'valid_to',
									'label'		=> __( 'Valid To', 'coschool' ),
									'type'      => 'date',
								],
							]
						]
					]
				],
			]
		];

		$metabox = apply_filters( 'coschool_coupon_metabox', $metabox, $this );

		new Metabox_API( $metabox );
	}
}