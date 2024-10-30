<?php
/**
 * All course related functions
 */
namespace Codexpert\CoSchool\App\Course;
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
class Post_Type {

	public function register() {
	
		$labels = array(
			'name'               	=> __( 'Courses', 'coschool' ),
			'singular_name'      	=> __( 'Course', 'coschool' ),
			'add_new'            	=> _x( 'Add New Course', 'coschool', 'coschool' ),
			'add_new_item'       	=> __( 'Add New Course', 'coschool' ),
			'edit_item'          	=> __( 'Edit Course', 'coschool' ),
			'new_item'           	=> __( 'New Course', 'coschool' ),
			'view_item'          	=> __( 'View Course', 'coschool' ),
			'search_items'       	=> __( 'Search Courses', 'coschool' ),
			'not_found'          	=> __( 'No Courses found', 'coschool' ),
			'not_found_in_trash' 	=> __( 'No Courses found in Trash', 'coschool' ),
			'parent_item_colon'  	=> __( 'Parent Course:', 'coschool' ),
			'menu_name'          	=> __( 'Courses', 'coschool' ),
			'featured_image'     	=> __( 'Thumbnail', 'coschool' ),
			'set_featured_image' 	=> __( 'Add Thumbnail', 'coschool' ),
			'remove_featured_image' => __( 'Remove Thumbnail', 'coschool' ),
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array( 'course-category' ),
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'coschool',
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'has_archive'         => 'courses',
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'             => array( 'slug' => 'course', 'with_front' => false ),
			'capability_type'     => 'post',
			'supports'            => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'comments',
			),
		);
	
		register_post_type( 'course', $args );
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
		$columns['author']			= __( 'Instructor', 'coschool' );
		
		$taxonomy_course_category	= $columns['taxonomy-course-category'];
		$taxonomy_course_difficulty	= $columns['taxonomy-course-difficulty'];
		$taxonomy_course_language	= $columns['taxonomy-course-language'];

		unset( $columns['date'] );
		unset( $columns['comments'] );
		unset( $columns['taxonomy-course-category'] );
		unset( $columns['taxonomy-course-difficulty'] );
		unset( $columns['taxonomy-course-language'] );

		$columns['price']							= __( 'Price', 'coschool' );
		$columns['contents']						= __( 'Contents', 'coschool' );
		$columns['students']						= __( 'Students', 'coschool' );
		$columns['earnings']						= __( 'Earnings', 'coschool' );
		$columns['taxonomy-course-category']		= $taxonomy_course_category;
		$columns['taxonomy-course-difficulty']		= $taxonomy_course_difficulty;
		$columns['taxonomy-course-language']		= $taxonomy_course_language;
		$columns['rating']							= __( 'Rating', 'coschool' );
		$columns['date']							= __( 'Date', 'coschool' );

		return $columns;
	}

	/**
	* Adds column content
	* 
	* @access public
	* 
 	* @param string $column the column id
 	* @param int $course_id item ID
	*/
	public function add_column_content( $column, $course_id ) {
		$course_data = new Data( $course_id );

		switch ( $column ) {

		    case 'contents' :
		    	printf( esc_html__( '%d Lessons', 'coschool' ), count( $course_data->get_lessons() ) );
		    	echo '<br />';
		    	printf( esc_html__( '%d Quizzes', 'coschool' ), count( $course_data->get_quizzes() ) );
		    	echo '<br />';
		    	printf( esc_html__( '%d Assignments', 'coschool' ), count( $course_data->get_assignments() ) );
		        break;

		    case 'students' :
		    	if( ( $count = count( $course_data->get_students() ) ) > 0 ) {
		    		echo '<a href="' . add_query_arg( [ 'page' => 'students', 'course_id' => $course_id ], admin_url( 'admin.php' ) ) . '">' . sprintf( _n( '%s Student', '%s Students', $count, 'coschool' ), number_format_i18n( $count ) ) . '</a>';
		    	}
		    	else {
		    		_e( '0 Students', 'coschool' );
		    	}
		        break;

		    case 'earnings' :
		        echo coschool_price( $course_data->get_earnings(), false );
		        break;

		    case 'rating' :
		        echo "<a href= 'edit-comments.php?p= " . esc_attr( $course_id ) . " '>" . coschool_populate_stars( $course_data->get( 'rating' ) ) . "</a>";
		        break;

		    case 'price' :
		        echo false === ( $price = $course_data->get( 'price' ) ) ? esc_html__( 'Free', 'coschool' ) : coschool_price( $price );
		        break;
		}
	}
	
	/**
	* Course updated text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function course_updated_message( $messages ){
		
		$text 		= "<a href ='" . get_the_permalink() . "'>". esc_html__( 'View Course', 'coschool' ) ."</a>";

		$messages['course'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => esc_html__( 'Course updated. ', 'coschool') .  wp_kses_post( $text ) ,
			6  => esc_html__( 'Course created. ', 'coschool') .  wp_kses_post( $text ) ,
			10 => esc_html__( 'Course draft updated. ', 'coschool' ) .  wp_kses_post( $text ) ,
		);

		return $messages;
	}

	/**
	* Bulk Course delete text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function bulk_course_updated_message( $bulk_messages, $bulk_counts ){
		
	    $bulk_messages['course'] = array(
	        'updated'   => _n( '%s Course updated.', '%s Courses updated.', $bulk_counts['updated'], 'coschool' ),
	        'locked'    => _n( '%s Course not updated, somebody is editing it.', '%s Courses not updated, somebody is editing them.', $bulk_counts['locked'], 'coschool' ),
	        'deleted'   => _n( '%s Course permanently deleted.', '%s Courses permanently deleted.', $bulk_counts['deleted'], 'coschool' ),
	        'trashed'   => _n( '%s Course moved to the Trash.', '%s Courses moved to the Trash.', $bulk_counts['trashed'], 'coschool' ),
	        'untrashed' => _n( '%s Course restored from the Trash.', '%s Courses restored from the Trash.', $bulk_counts['untrashed'], 'coschool' ),
	    );
	 
	    return $bulk_messages;
	}
}