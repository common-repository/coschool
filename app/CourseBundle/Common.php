<?php
namespace Codexpert\CoSchool\App\CourseBundle;
use Codexpert\CoSchool\App\CourseBundle;
use Codexpert\CoSchool\App\Bundle\Data as Bundle_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;

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
class Common {

	public function register_cpt() {
	
		$labels = array(
			'name'               	=> __( 'Bundles', 'coschool' ),
			'singular_name'      	=> __( 'Bundle', 'coschool' ),
			'add_new'            	=> _x( 'Add New Bundle', 'coschool', 'coschool' ),
			'add_new_item'       	=> __( 'Add New Bundle', 'coschool' ),
			'edit_item'          	=> __( 'Edit Bundle', 'coschool' ),
			'new_item'           	=> __( 'New Bundle', 'coschool' ),
			'view_item'          	=> __( 'View Bundle', 'coschool' ),
			'search_items'       	=> __( 'Search Bundles', 'coschool' ),
			'not_found'          	=> __( 'No Bundles found', 'coschool' ),
			'not_found_in_trash' 	=> __( 'No Bundles found in Trash', 'coschool' ),
			'parent_item_colon'  	=> __( 'Parent Bundle:', 'coschool' ),
			'menu_name'          	=> __( 'Bundles', 'coschool' ),
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
			'show_in_admin_bar'		=> true,
			'menu_position'			=> null,
			'menu_icon'				=> null,
			'show_in_nav_menus'		=> true,
			'publicly_queryable'  	=> true,
			'exclude_from_search' 	=> true,
			'has_archive'			=> false,
			'query_var'				=> true,
			'can_export'			=> true,
			'rewrite'				=> [ 'slug' => '/bundle', 'with_front' => true ],
			'capability_type'		=> 'post',
			'supports'				=> [ 'title', 'editor' ],
		);
	
		register_post_type( 'bundle', $args );
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

		$columns['courses']		= __( 'Courses', 'coschool' );
		$columns['price']		= __( 'Price', 'coschool' );
		$columns['enroll']		= __( 'Enroll URL', 'coschool' );
		$columns['date']		= __( 'Date', 'coschool' );

		return $columns;
	}

	/**
	* Adds column content
	* 
	* @access public
	* 
 	* @param string $column the column id
 	* @param int $bundle_id item ID
	*/
	public function add_column_content( $column, $bundle_id ) {

		$admin_url		= admin_url( 'admin.php' );
		$bundle_data 	= new Data( $bundle_id );

		switch ( $column ) {

		    case 'courses' :
				$bundle_id		= $bundle_data->get( 'ID' );
				$bundle_courses	= $bundle_data->get_courses() ;

				if( is_array( $bundle_courses ) && count( $bundle_courses ) > 0 ) {
					echo '<ul class="coschool-bundle-courses">';
					foreach ($bundle_courses as $course_id) {
						$course_data = new Course_Data( $course_id );
						printf( '<li><a href="%1$s">%2$s</a></li>', get_edit_post_link( $course_data->get( 'id' ) ), $course_data->get( 'name' ) );
					}
					echo '</ul>';
				}
		    break;

		    case 'enroll' :
		    	$enroll_url = apply_filters( 'bundle_enroll_url', add_query_arg( 'enroll', $bundle_id, coschool_enroll_page( true ) ), $bundle_id );
		    	// printf( '<input type="text" class="coschool-bundle-url" value="%s" readonly />', $enroll_url );
				printf( '<input type="text" class="coschool-bundle-url" value="%s" readonly />
                    <img src="http://clipground.com/images/copy-4.png" class="course-bundle-icon" title="Click to Copy" >
                    ', $enroll_url );
		    break;

		    case 'price' :
		    	echo coschool_price( $bundle_data->get_price() );
		    break;
		}
	}
}