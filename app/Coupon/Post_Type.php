<?php
/**
 * All coupon related functions
 */
namespace Codexpert\CoSchool\App\Coupon;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Instructor\Data as Instructor_Data;

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
class Post_Type {

	public function register() {
	
		$labels = array(
			'name'					=> __( 'Coupons', 'coschool' ),
			'singular_name'			=> __( 'Coupon', 'coschool' ),
			'add_new'				=> _x( 'Add New Coupon', 'coschool', 'coschool' ),
			'add_new_item'			=> __( 'Add New Coupon', 'coschool' ),
			'edit_item'				=> __( 'Edit Coupon', 'coschool' ),
			'new_item'				=> __( 'New Coupon', 'coschool' ),
			'view_item'				=> __( 'View Coupon', 'coschool' ),
			'search_items'			=> __( 'Search Coupons', 'coschool' ),
			'not_found'				=> __( 'No Coupons found', 'coschool' ),
			'not_found_in_trash' 	=> __( 'No Coupons found in Trash', 'coschool' ),
			'parent_item_colon'		=> __( 'Parent Coupon:', 'coschool' ),
			'menu_name'				=> __( 'Coupons', 'coschool' ),
			'featured_image'     	=> __( 'Banner', 'coschool' ),
			'set_featured_image' 	=> __( 'Add Banner', 'coschool' ),
			'remove_featured_image'	=> __( 'Remove Banner', 'coschool' ),
		);
	
		$args = array(
			'labels'				=> $labels,
			'hierarchical'			=> false,
			'description'			=> 'description',
			'taxonomies'			=> array(),
			'public'				=> false,
			'show_ui'				=> true,
			'show_in_menu'			=> 'coschool',
			'show_in_admin_bar'		=> false,
			'menu_position'			=> null,
			'menu_icon'				=> null,
			'show_in_nav_menus'		=> false,
			'publicly_queryable'  	=> false,
			'exclude_from_search' 	=> true,
			'has_archive'			=> false,
			'query_var'				=> true,
			'can_export'			=> true,
			'rewrite'				=> true,
			'capability_type'		=> 'page',
			'supports'				=> array( 'title' ),
		);
	
		register_post_type( 'coupon', $args );
	}

	/**
	* Adds table column
	* 
	* @access public
	* 
 	* @param array $columns
 	* 
	* @return array $columns
	*/
	public function add_table_columns( $columns ) {
		unset( $columns['date'] );

		$columns['applies']			= __( 'Applies to', 'coschool' );
		$columns['discount-type']	= __( 'Discount Type', 'coschool' );
		$columns['discount-amount']	= __( 'Discount amount', 'coschool' );
		$columns['validity']		= __( 'Validity Period', 'coschool' );
		$columns['date']			= __( 'Date', 'coschool' );

		return $columns;
	}

	/**
	* Adds column content
	* 
	* @access public
	* 
 	* @param string $column the column id
 	* @param int $coupon_id item ID
	*/
	public function add_column_content( $column, $coupon_id ) {

		$coupon_data 	= new Data( $coupon_id );

		switch ( $column ) {

		    case 'applies' :
				$condition 		= $coupon_data->get( 'coschool_condition' );
				$filter_by 		= $condition['filter_by'];

				if ( ! empty( $filter_by ) && 'courses' == $filter_by ) {
					$courses 		= isset( $condition['courses'] ) ? $condition['courses'] : '';
					$course_names	= [];

					if ( ! empty( $courses ) ) {

						foreach ( $courses as $course_id ) {
							$course_data	= new Data( $course_id );
					    	$course_names[] = '<a href="' . get_permalink( $course_data->get( 'id' ) ) . '">' . $course_data->get( 'name' ) . '</a>';
						}

						echo wp_kses_post( implode( ', ', $course_names ) );
					}
					else {
						echo '<p>'. esc_html( 'Can\'t selected any course' , 'coschool' ) .'</p>';
					}
				}
				elseif ( ! empty( $filter_by ) && 'instructors' == $filter_by ) {
					$instructors = isset( $condition['instructors'] ) ? $condition['instructors'] : '';

					if ( ! empty( $instructors ) ) {

						foreach ( $instructors as $instructor_id ) {
							$instructor_data	= new Instructor_Data( $instructor_id );
					    	$instructor_names[] = '<a href="' . get_author_posts_url( $instructor_data->get( 'id' ) ) . '">' . $instructor_data->get( 'name' ) . '</a>';
						}

						echo wp_kses_post( implode( ', ', $instructor_names ) );
					}
					else {
						echo '<p>'. esc_html( 'Can\'t selected any instructor' , 'coschool' ) .'</p>';
					}
				}
				elseif ( ! empty( $filter_by ) && 'categories' == $filter_by ) {
					$categories = isset( $condition['categories'] ) ? $condition['categories'] : '';

					if ( ! empty( $categories ) ) {

						foreach ( $categories as $category_id ) {
					    	$instructor_names[] = '<a href="' . get_category_link( $category_id ) . '">' . get_the_category_by_ID( $category_id ) . '</a>';
						}

						echo wp_kses_post( implode( ', ', $instructor_names ) );
					}
					else {
						echo '<p>'. esc_html_e( 'Can\'t selected any category' , 'coschool' ) .'</p>';
					}

				}
				else {
					esc_html_e( 'All Applies', 'coschool' );
				}
				
		    break;

		    case 'discount-type' :
				$discount 		= $coupon_data->get( 'coschool_discount' );
				$discount_type 	= isset( $discount['discount_type'] ) ? ucwords( $discount['discount_type'] ) : '';

				echo "<p>" . esc_html( $discount_type ) . "</p>";
		    break;

		    case 'discount-amount' :
				$discount 			= $coupon_data->get( 'coschool_discount' );
				$discount_amount 	= isset( $discount['amount'] ) ? coschool_price( $discount['amount'] ) : '';

				echo "<p>" . esc_html( $discount_amount ) . "</p>";
		    break;

		    case 'validity' :
				$condition 		= $coupon_data->get( 'coschool_condition' );
				$has_validity 	= isset( $condition['has_validity'] ) ? $condition['has_validity'] : '';
				$format 		= get_option( 'date_format' );
				$valid_from 	= isset( $condition['valid_from'] ) ? date( $format, strtotime( $condition['valid_from'] ) ) : '';
				$valid_to 		= isset( $condition['valid_to'] ) ? date( $format, strtotime( $condition['valid_to'] ) ) : '';

				if ( ! is_null( $has_validity ) && 'on' == $has_validity ) {
					echo "<p><span>" . esc_html( $valid_from ) . "</span> to <span>" . esc_html( $valid_to ) . "</span></p>";
				}

		    break;
		}
	}

	/**
	* Coupon updated text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function coupon_updated_message( $messages ){

		$messages['coupon'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Coupon updated. ', 'coschool' ),
			6  => __( 'Coupon created. ', 'coschool' ),
		);

		return $messages;
	}

	/**
	* Bulk Coupon delete text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function bulk_coupon_updated_message( $bulk_messages, $bulk_counts ){
		
	    $bulk_messages['coupon'] = array(
	        'updated'   => _n( '%s Coupon updated.', '%s Coupons updated.', $bulk_counts['updated'], 'coschool' ),
	        'locked'    => _n( '%s Coupon not updated, somebody is editing it.', '%s Coupons not updated, somebody is editing them.', $bulk_counts['locked'], 'coschool' ),
	        'deleted'   => _n( '%s Coupon permanently deleted.', '%s Coupons permanently deleted.', $bulk_counts['deleted'], 'coschool' ),
	        'trashed'   => _n( '%s Coupon moved to the Trash.', '%s Coupons moved to the Trash.', $bulk_counts['trashed'], 'coschool' ),
	        'untrashed' => _n( '%s Coupon restored from the Trash.', '%s Coupons restored from the Trash.', $bulk_counts['untrashed'], 'coschool' ),
	    );
	 
	    return $bulk_messages;
	}
}