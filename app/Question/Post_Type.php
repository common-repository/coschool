<?php
/**
 * All quiz related functions
 */
namespace Codexpert\CoSchool\App\Question;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Question
 * @author Codexpert <hi@codexpert.io>
 */
class Post_Type {

	public function register() {
	
		$labels = array(
			'name'               => __( 'Questions', 'coschool' ),
			'singular_name'      => __( 'Question', 'coschool' ),
			'add_new'            => _x( 'Add New Question', 'coschool', 'coschool' ),
			'add_new_item'       => __( 'Add New Question', 'coschool' ),
			'edit_item'          => __( 'Edit Question', 'coschool' ),
			'new_item'           => __( 'New Question', 'coschool' ),
			'view_item'          => __( 'View Question', 'coschool' ),
			'search_items'       => __( 'Search Questions', 'coschool' ),
			'not_found'          => __( 'No Questions found', 'coschool' ),
			'not_found_in_trash' => __( 'No Questions found in Trash', 'coschool' ),
			'parent_item_colon'  => __( 'Parent Question:', 'coschool' ),
			'menu_name'          => __( 'Questions', 'coschool' ),
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => true,
			'capability_type'     => 'post',
			'supports'            => array( 'title' ),
		);
	
		register_post_type( 'question', $args );
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

		$columns['quiz']	= __( 'Quiz', 'coschool' );
		$columns['date']	= __( 'Date', 'coschool' );

		return $columns;
	}

	/**
	* Adds column content
	* 
	* @access public
	* 
 	* @param string $column the column id
 	* @param int $certificate_id item ID
	*/
	public function add_column_content( $column, $certificate_id ) {

		switch ( $column ) {

		    case 'quiz' :
				$certificate_data 	= new Data( $certificate_id );
				$quiz_id 			= $certificate_data->get( 'quiz_id' );

				if ( ! is_null( $quiz_id ) ) {
					$quiz_data	= new Data( $quiz_id );
			 		echo '<a href="' . esc_attr( get_edit_post_link( $quiz_data->get( 'id' ) ) ) . '">'. esc_html( $quiz_data->get( 'name' ) ) . '</a>';
				}
				
		    break;
		}
	}
}