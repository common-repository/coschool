<?php
/**
 * All quiz related functions
 */
namespace Codexpert\CoSchool\App\Quiz;
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
 * @subpackage Quiz
 * @author Codexpert <hi@codexpert.io>
 */
class Post_Type {

	public function register() {
	
		$labels = array(
			'name'               	=> __( 'Quizzes', 'coschool' ),
			'singular_name'      	=> __( 'Quiz', 'coschool' ),
			'add_new'            	=> _x( 'Add New Quiz', 'coschool', 'coschool' ),
			'add_new_item'       	=> __( 'Add New Quiz', 'coschool' ),
			'edit_item'          	=> __( 'Edit Quiz', 'coschool' ),
			'new_item'           	=> __( 'New Quiz', 'coschool' ),
			'view_item'          	=> __( 'View Quiz', 'coschool' ),
			'search_items'       	=> __( 'Search Quizzes', 'coschool' ),
			'not_found'          	=> __( 'No Quizzes found', 'coschool' ),
			'not_found_in_trash' 	=> __( 'No Quizzes found in Trash', 'coschool' ),
			'parent_item_colon'  	=> __( 'Parent Quiz:', 'coschool' ),
			'menu_name'          	=> __( 'Quizzes', 'coschool' ),
			'featured_image'     	=> __( 'Banner', 'coschool' ),
			'set_featured_image' 	=> __( 'Add Banner', 'coschool' ),
			'remove_featured_image'	=> __( 'Remove Banner', 'coschool' ),
		);
	
		$args = array(
			'labels'              => $labels,
			'hierarchical'        => false,
			'description'         => 'description',
			'taxonomies'          => array(),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'coschool',
			'show_in_admin_bar'   => true,
			'menu_position'       => null,
			'menu_icon'           => null,
			'show_in_nav_menus'   => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'has_archive'         => true,
			'query_var'           => true,
			'can_export'          => true,
			'rewrite'				=> [ 'slug' => 'course/%course%/quiz', 'with_front' => true ],
			'capability_type'     => 'post',
			'supports'            => array( 'title', 'editor' ),
		);
	
		register_post_type( 'quiz', $args );
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
		$columns['attempts']	= __( 'Attempts', 'coschool' );
		$columns['date']		= __( 'Date', 'coschool' );

		return $columns;
	}

	/**
	* Adds column content
	* 
	* @access public
	* 
 	* @param string $column the column id
 	* @param int $quiz_id item ID
	*/
	public function add_column_content( $column, $quiz_id ) {
		$quiz_data 	= new Data( $quiz_id );
		$admin_url	= admin_url( 'admin.php' );

		switch ( $column ) {

		    case 'course' :
				$course_id 	= $quiz_data->get( 'course_id' );

				if ( ! is_null( $course_id ) ) {
					$course_data = new Data( $course_id );
			    	echo '<a href="' . esc_attr( get_edit_post_link( $course_data->get( 'id' ) ) ) . '">' . esc_html( $course_data->get( 'name' ) ) . '</a>';
				}

			break;

		    case 'attempts' :
		    	$attempt_count = count( $quiz_data->get_attempts() );

		    	if( $attempt_count < 1 ) {
		    		esc_html_e( '0 Attempts' );
		    	}
		    	else {
					printf( wp_kses_post( '<a href="%1$s">%2$d Attempts</a>', 'coschool' ), add_query_arg( [ 'page' => 'quiz-attempts', 'quiz' => $quiz_id ], $admin_url ), $attempt_count );
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
		$quiz_data = new Data( $post );

        if ( is_object( $quiz_data ) && 'quiz' == get_post_type( $post ) ) {
            $course_id = $quiz_data->get( 'course_id' );

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

    	if( $post->post_type != 'quiz' ) return $content;

    	$quiz_data = new Data( $post );

    	if( ! $quiz_data->is_visible_by() ) {
    		include Helper::locate_template( 'no-access.php' );
    		return;
    	}

    	$student_data = new Student_Data( get_current_user_id() );
    	if( $student_data->has_course( $quiz_data->get_course() ) ) {
    		$student_data->set( 'coschool_last_seen_' . $quiz_data->get_course(), $post->ID );
    	}

    	return $content;
    }

    public function add_menu() {
    	add_submenu_page( 'coschool', __( 'Quiz Attempts', 'coschool' ), __( 'Quiz Attempts', 'coschool' ), 'create_courses', 'quiz-attempts', [ $this, 'callback_attempts' ] );
    }

    public function callback_attempts() {
    	if( isset( $_GET['quiz'] ) ) {
    		echo Helper::get_view( 'quiz-attempts', 'views/adminmenu' );
    	}
    	elseif( isset( $_GET['attempt'] ) ) {
    		echo Helper::get_view( 'attempt-answers', 'views/adminmenu' );
    	}
    }

    public function highlight_menu( $submenu_file ) {

        global $plugin_page;

        $hidden_submenus = array(
            'quiz-attempts' => true,
        );

        // Select another submenu item to highlight (optional).
        if ( $plugin_page && isset( $hidden_submenus[ $plugin_page ] ) ) {
            $submenu_file = 'edit.php?post_type=quiz';
        }

        // Hide the submenu.
        foreach ( $hidden_submenus as $submenu => $unused ) {
            remove_submenu_page( 'coschool', $submenu );
        }

        return $submenu_file;
    }

	/**
	* Quiz updated text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function quiz_updated_message( $messages ){
		
		$text 		= "<a href ='" . get_the_permalink() . "'>View Quiz</a>";

		$messages['quiz'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => esc_html( 'Quiz updated. ', 'coschool') . $text,
			6  => esc_html( 'Quiz created. ', 'coschool') . $text,
		);

		return $messages;
	}

	/**
	* Bulk Quiz delete text
	* 
	* @access public
	* 
 	* @param string mesages
	*/
	public function bulk_quiz_updated_message( $bulk_messages, $bulk_counts ){
		
	    $bulk_messages['quiz'] = array(
	        'updated'   => _n( '%s Quiz updated.', '%s Quizs updated.', $bulk_counts['updated'], 'coschool' ),
	        'locked'    => _n( '%s Quiz not updated, somebody is editing it.', '%s Quizs not updated, somebody is editing them.', $bulk_counts['locked'], 'coschool' ),
	        'deleted'   => _n( '%s Quiz permanently deleted.', '%s Quizs permanently deleted.', $bulk_counts['deleted'], 'coschool' ),
	        'trashed'   => _n( '%s Quiz moved to the Trash.', '%s Quizs moved to the Trash.', $bulk_counts['trashed'], 'coschool' ),
	        'untrashed' => _n( '%s Quiz restored from the Trash.', '%s Quizs restored from the Trash.', $bulk_counts['untrashed'], 'coschool' ),
	    );
	 
	    return $bulk_messages;
	}
}