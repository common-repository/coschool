<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Lesson;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Course\Data as Course_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Lesson
 * @author Codexpert <hi@codexpert.io>
 */
class Post_Type {

	public function register() {
	
		$labels = array(
			'name'					=> __( 'Lessons', 'coschool' ),
			'singular_name'			=> __( 'Lesson', 'coschool' ),
			'add_new'				=> _x( 'Add New Lesson', 'coschool', 'coschool' ),
			'add_new_item'			=> __( 'Add New Lesson', 'coschool' ),
			'edit_item'				=> __( 'Edit Lesson', 'coschool' ),
			'new_item'				=> __( 'New Lesson', 'coschool' ),
			'view_item'				=> __( 'View Lesson', 'coschool' ),
			'search_items'			=> __( 'Search Lessons', 'coschool' ),
			'not_found'				=> __( 'No Lessons found', 'coschool' ),
			'not_found_in_trash' 	=> __( 'No Lessons found in Trash', 'coschool' ),
			'parent_item_colon'		=> __( 'Parent Lesson:', 'coschool' ),
			'menu_name'				=> __( 'Lessons', 'coschool' ),
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
			'rewrite'				=> [ 'slug' => 'course/%course%/lesson', 'with_front' => true ],
			'capability_type'		=> 'post',
			'supports'				=> array( 'title', 'editor', 'comments', 'thumbnail' ),
		);
	
		register_post_type( 'lesson', $args );
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

		$columns['course']	= __( 'Course', 'coschool' );
		$columns['date']	= __( 'Date', 'coschool' );

		return $columns;
	}

	/**
	* Adds column content
	* 
	* @access public
	* 
 	* @param string $column the column id
 	* @param int $lesson_id item ID
	*/
	public function add_column_content( $column, $lesson_id ) {

		switch ( $column ) {

		    case 'course' :
				$lesson_data 	= new Data( $lesson_id );
				$course_id 		= $lesson_data->get( 'course_id' );

				if ( ! is_null( $course_id ) ) {
					$course_data	= new Data( $course_id );
			    	echo '<a href="' . esc_attr( get_edit_post_link( $course_data->get( 'id' ) ) ) . '">' . esc_html( $course_data->get( 'name' ) ) . '</a>';
				}
				
		    break;
		}
	}

	/**
	* Update lesson permalink
	* 
	* @access public
	* 
 	* @param string $url the URL
 	* @param obj $post the post object
 	* @param bool $leavename
 	* 
	* @return string $url the URL
	*/
	public function permalink( $url, $post, $leavename ) {
		$lesson_data = new Data( $post );

        if ( is_object( $lesson_data ) && 'lesson' == get_post_type( $post ) ) {
            $course_id = $lesson_data->get( 'course_id' );

            if ( ! is_null( $course_id ) ) {
	            $course_data 	= new Course_Data( $course_id );
	            $url 			= str_replace( '%course%', $course_data->get( 'post_name' ), $url );
            }
            else {
            	$url = str_replace( '%course%', '[undefined]', $url );
            }
        }

        return $url;
    }

    /**
     * Filters the content
     * 
     * @since 0.9
     * 
     * @return string $content filtered or original
     */
    public function filter_content( $content ) {
    	global $post;

    	if( $post->post_type != 'lesson' ) return $content;

    	$lesson_data = new Data( $post );

    	if( ! $lesson_data->is_visible_by() ) {
    		include Helper::locate_template( 'no-access.php' );
    		return;
    	}

    	$student_data = new Student_Data( get_current_user_id() );
    	if( $student_data->has_course( $lesson_data->get_course() ) ) {
    		$student_data->set( 'coschool_last_seen_' . $lesson_data->get_course(), $post->ID );
    	}

    	return $content;
    }

	/**
	* Lesson updated text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function lesson_updated_message( $messages ){
		
		$text 		= "<a href ='" . get_the_permalink()  . "'>View Lesson</a>";

		$messages['lesson'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => esc_html( 'Lesson updated. ', 'coschool' ) . wp_kses_post( $text ),
			6  => esc_html( 'Lesson created. ', 'coschool' ) . wp_kses_post( $text ),
		);

		return $messages;
	}

	/**
	* Bulk Lesson delete text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function bulk_lesson_updated_message( $bulk_messages, $bulk_counts ){
		
	    $bulk_messages['lesson'] = array(
	        'updated'   => _n( '%s Lesson updated.', '%s Lessons updated.', $bulk_counts['updated'], 'coschool' ),
	        'locked'    => _n( '%s Lesson not updated, somebody is editing it.', '%s Lessons not updated, somebody is editing them.', $bulk_counts['locked'], 'coschool' ),
	        'deleted'   => _n( '%s Lesson permanently deleted.', '%s Lessons permanently deleted.', $bulk_counts['deleted'], 'coschool' ),
	        'trashed'   => _n( '%s Lesson moved to the Trash.', '%s Lessons moved to the Trash.', $bulk_counts['trashed'], 'coschool' ),
	        'untrashed' => _n( '%s Lesson restored from the Trash.', '%s Lessons restored from the Trash.', $bulk_counts['untrashed'], 'coschool' ),
	    );
	 
	    return $bulk_messages;
	}
}