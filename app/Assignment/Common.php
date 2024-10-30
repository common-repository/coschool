<?php
/**
 * All common facing functions
 */
namespace Codexpert\CoSchool\App\Assignment;
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
 * @subpackage Common
 * @author Codexpert <hi@codexpert.io>
 */
class Common {

	/**
	 * Constructor function
	 */
	public function __construct() {
	}

	public function register_cpt() {
	
		$labels = array(
			'name'               	=> __( 'Assignments', 'coschool' ),
			'singular_name'      	=> __( 'Assignment', 'coschool' ),
			'add_new'            	=> _x( 'Add New Assignment', 'coschool', 'coschool' ),
			'add_new_item'       	=> __( 'Add New Assignment', 'coschool' ),
			'edit_item'          	=> __( 'Edit Assignment', 'coschool' ),
			'new_item'           	=> __( 'New Assignment', 'coschool' ),
			'view_item'          	=> __( 'View Assignment', 'coschool' ),
			'search_items'       	=> __( 'Search Assignments', 'coschool' ),
			'not_found'          	=> __( 'No Assignments found', 'coschool' ),
			'not_found_in_trash' 	=> __( 'No Assignments found in Trash', 'coschool' ),
			'parent_item_colon'  	=> __( 'Parent Assignment:', 'coschool' ),
			'menu_name'          	=> __( 'Assignments', 'coschool' ),
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
			'rewrite'				=> [ 'slug' => 'course/%course%/assignment', 'with_front' => true ],
			'capability_type'		=> 'post',
			'supports'				=> array( 'title', 'editor', ),
		);
	
		register_post_type( 'assignment', $args );
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

		$columns['course']		= __( 'Course', 'coschool' );
		$columns['submission']	= __( 'Submissions', 'coschool' );
		$columns['date']		= __( 'Date', 'coschool' );

		return $columns;
	}

	/**
	* Adds column content
	* 
	* @access public
	* 
 	* @param string $column the column id
 	* @param int $assignment_id item ID
	*/
	public function add_column_content( $column, $assignment_id ) {

		$admin_url	= admin_url( 'admin.php' );
		$assignment_data 	= new Data( $assignment_id );

		switch ( $column ) {

		    case 'course' :
				$course_id = $assignment_data->get( 'course_id' );

				if ( ! is_null( $course_id ) ) {
					$course_data = new Data( $course_id );
			    	echo '<a href="' . get_edit_post_link( $course_data->get( 'id' ) ) . '">'. $course_data->get( 'name' ) . '</a>';
				}
		    break;

		    case 'submission' :
		    	if( count( $submissions = $assignment_data->get( 'submissions' ) ) > 0 ) {
					printf( __( '<a href="%1$s">%2$d Submissions</a>', 'coschool' ), add_query_arg( [ 'page' => 'assignment-submissions', 'assignment' => $assignment_data->get( 'id' ) ], $admin_url ), count( $submissions ) );
		    	}
		    	else {
		    		_e( '0 Submissions' );
		    	}

		    break;
		}
	}


    public function add_menu() {
    	add_submenu_page( 'coschool', __( 'Assignment Submissions', 'coschool' ), __( 'Assignment Submissions', 'coschool' ), 'create_courses', 'assignment-submissions', [ $this, 'callback_submissions' ] );
    }

    public function callback_submissions() {
    	if( isset( $_GET['assignment'] ) ) {
    		echo Helper::get_template( 'assignment-submissions', 'views/adminmenu' );
    	}
    	elseif( isset( $_GET['submission'] ) ) {
    		echo Helper::get_template( 'submission-answers', 'views/adminmenu' );
    	}
    }

    public function highlight_menu( $submenu_file ) {

        global $plugin_page;

        $hidden_submenus = array(
            'assignment-submissions' => true,
        );

        // Select another submenu item to highlight (optional).
        if ( $plugin_page && isset( $hidden_submenus[ $plugin_page ] ) ) {
            $submenu_file = 'edit.php?post_type=assignment';
        }

        // Hide the submenu.
        foreach ( $hidden_submenus as $submenu => $unused ) {
            remove_submenu_page( 'coschool', $submenu );
        }

        return $submenu_file;
    }

	/**
	* Update assignment permalink
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
		$assignment_data = new Data( $post );

        if ( is_object( $assignment_data ) && 'assignment' == get_post_type( $post ) ) {
            $course_id = $assignment_data->get( 'course_id' );

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

    	if( $post->post_type != 'assignment' ) return $content;

    	$assignment_data = new Data( $post );

    	if( ! $assignment_data->is_visible_by() ) {
    		include Helper::locate_template( 'no-access.php' );
    		return;
    	}

    	$student_data = new Student_Data( get_current_user_id() );
    	if( $student_data->has_course( $assignment_data->get_course() ) ) {
    		$student_data->set( 'coschool_last_seen_' . $assignment_data->get_course(), $post->ID );
    	}

    	return $content;
    }

	/**
	* Assignment updated text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function assignment_updated_message( $messages ){
		

		$text 		= "<a href ='" . get_the_permalink() . "'>View Assignment</a>";

		$messages['assignment'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Assignment updated. ', 'coschool' ) . $text,
			6  => __( 'Assignment created. ', 'coschool' ) . $text,

		);

		return $messages;
	}

	/**
	* Bulk Assignment delete text
	* 
 	* @param string mesages
	*/
	public function bulk_assignment_updated_message( $bulk_messages, $bulk_counts ){
		
	    $bulk_messages['assignment'] = array(
	        'updated'   => _n( '%s Assignment updated.', '%s Assignments updated.', $bulk_counts['updated'], 'coschool' ),
	        'locked'    => _n( '%s Assignment not updated, somebody is editing it.', '%s Assignments not updated, somebody is editing them.', $bulk_counts['locked'], 'coschool' ),
	        'deleted'   => _n( '%s Assignment permanently deleted.', '%s Assignments permanently deleted.', $bulk_counts['deleted'], 'coschool' ),
	        'trashed'   => _n( '%s Assignment moved to the Trash.', '%s Assignments moved to the Trash.', $bulk_counts['trashed'], 'coschool' ),
	        'untrashed' => _n( '%s Assignment restored from the Trash.', '%s Assignments restored from the Trash.', $bulk_counts['untrashed'], 'coschool' ),
	    );
	 
	    return $bulk_messages;
	}
}