<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Course;
use Codexpert\CoSchool\Abstracts\Post_Data;
use Codexpert\CoSchool\Helper;
use Codexpert\CoSchool\App\Student\Data as Student_Data;
use Codexpert\CoSchool\App\Certificate\Data as Certificate_Data;

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
class Data extends Post_Data {

	/**
	 * @var obj
	 */
	public $course;

	/**
	 * @var int
	 */
	public $student;

	/**
	 * Constructor function
	 * 
	 * @uses WP_User class
	 * @param int|obj $course the course
	 */
	public function __construct( $course ) {
		$this->course = get_post( $course );
		parent::__construct( $this->course );

		$this->student = new Student_Data( get_current_user_id() );
	}

	/**
	 * The price
	 * 
	 * @return bool or float|int
	 */
	public function get_price() {
		$pricing = $this->get( 'coschool_pricing' );

		if( ! isset( $pricing['price_type'] ) || $pricing['price_type'] != 'premium' || ! isset( $pricing['price'] ) || '' == $pricing['price'] ) {
			return 0;
		}

		return $pricing['price'];
	}
	/**
	 * keywords
	 * 
	 * @return str
	 */
	public function get_keywords() {
		$keywords = $this->get( 'coschool_advanced' );

		if( ! isset( $keywords['meta_keyword'] ) ){
			return '';
		}

		return $keywords['meta_keyword'];
	}


	/**
	 * Course type
	 * 
	 * @return free|premium
	 */
	public function get_type() {
		return 0 === $this->get_price() ? 'free' : 'premium';
	}

	/**
	 * Course type
	 * 
	 * @return free|premium
	 */
	public function get_status() {
		return get_post_status( $this->get( 'id' ) );
	}

	/**
	 * Gets the course thumbnail
	 * 
	 * @return string The URL
	 */
	public function get_thumbnail( $size = 'coschool-thumb' ) {
		$img 			= plugins_url( 'assets/img/no-thumbnail.jpg', COSCHOOL );
		$default_img 	= "<img src='{$img}' class='attachment-coschool-banner size-coschool-banner wp-post-image'>";
		return get_the_post_thumbnail( $this->get( 'id' ), $size ) ? : $default_img;
	}

	/**
	 * Gets the course thumbnail id
	 * 
	 * @return int The ID
	 */
	public function get_thumbnail_id() {
		return get_post_meta( $this->get( 'id' ), '_thumbnail_id', true );
	}

	/**
	 * Gets the course thumbnail url
	 * 
	 * @return string The URL
	 */
	public function get_thumbnail_url( $size = 'coschool-thumb' ) {
		$default_img = plugins_url( 'assets/img/no-thumbnail.jpg', COSCHOOL );
		return get_the_post_thumbnail_url( $this->get( 'id' ), $size ) ? : $default_img;
	}

	/**
	 * Gets the course banner
	 * 
	 * @return string The URL
	 */
	public function get_banner( $size = 'coschool-banner' ) {

		// if we have a banner added
		if( '' != ( $banner_id = get_post_meta( $this->get( 'id' ), 'course_banner', true ) ) ) {
			$banner_meta = wp_get_attachment_metadata( $banner_id );

			// it's a viedo banner
			if( isset( $banner_meta['mime_type'] ) && false !== strpos( $banner_meta['mime_type'], 'video' ) ) {
				$video_url = wp_get_attachment_url( $banner_id );
				
				return "
				<video class='coschool-video coschool-video-banner' width='{$banner_meta['width']}' height='{$banner_meta['height']}' controls>
					<source src='{$video_url}' type='video/mp4'>
					<source src='{$video_url}' type='video/ogg'>
				</video>";
			}

			// it's an image banner
			else {
				return wp_get_attachment_image( $banner_id, $size );
			}
		}
		
		// banner not added
		return "<img src='" . plugins_url( 'assets/img/no-banner.jpg', COSCHOOL ) . "' class=''>";
	}

	public function get_banner_id() {
		return get_post_meta( $this->get( 'id' ), 'course_banner', true );
	}

	/**
	 * Gets the course banner url
	 * 
	 * @return string The URL
	 */
	public function get_banner_url( $size = 'coschool-banner' ) {
		$default_img 	= plugins_url( 'assets/img/no-thumbnail.jpg', COSCHOOL );
		$banner_id 		= get_post_meta( $this->get( 'id' ), 'course_banner', true );

		return wp_get_attachment_image_url( $banner_id, $size ) ? : $default_img;
	}

	/**
	 * Gets the ID of all content associated this course including lessons, quizzes and assignments
	 * 
	 * @return [int|obj] array of posts $post_id|$post
	 */
	public function get_contents( $type = '', $details = false ) {
		$all_contents = $this->get( 'course_contents', [] );

		$items = [];
		foreach ( $all_contents as $chapter => $contents ) {
			foreach ( $contents as $item_id ) {

				$item = get_post( $item_id );

				if( $type != '' && in_array( $type, [ 'lesson', 'quiz', 'assignment' ] ) ) {
					if( isset( $item->post_type ) && $item->post_type == $type ) {
						$items[ $chapter ][ $item_id ] = $item;
					}
				}
				else {
					$items[ $chapter ][ $item_id ] = $item;
				}

			}
		}

		if( $details ) {
			return $items;
		}

		$_values = [];
		foreach ( array_values( $items ) as $chapters ) {
			foreach ( $chapters as $id => $post ) {
				$_values[] = $id;
			}
		}

		return $_values;
	}

	/**
	 * Gets the ID of all lessons associated this course
	 * 
	 * @return [int|obj] array of the lesson IDs $post_id|$post
	 */
	public function get_lessons( $details = false ) {
		return $this->get_contents( 'lesson', $details );
	}

	/**
	 * Gets the ID of all quizzes associated this course
	 * 
	 * @return [int|obj] array of the quiz IDs $post_id|$post
	 */
	public function get_quizzes( $details = false ) {
		return $this->get_contents( 'quiz', $details );
	}

	/**
	 * Gets the ID of all assignments associated this course
	 * 
	 * @return [int|obj] array of the assignment IDs $post_id|$post
	 */
	public function get_assignments( $details = false ) {
		return $this->get_contents( 'assignment', $details );
	}

	/**
	 * Gets the ID of all notices associated this course
	 * 
	 * @return [int|obj] array of the assignment IDs $post_id|$post
	 */
	public function get_notices( $details = false ) {
		$notices = Helper::get_posts( [ 'post_type' => 'notice', 'meta_key' => 'notice_course_id', 'meta_value' => $this->get( 'id' ) ], false, false, $details );

		return $notices;
	}

	/**
	 * Gets the instructor ID
	 * 
	 * @return int ID of the instructor
	 */
	public function get_instructor() {
		return $this->get( 'author' );
	}

	/**
	 * Gets associated course categories
	 * 
	 * @return [$term_id]
	 */
	public function get_categories() {
		$categories = [];

		$terms = get_the_terms( $this->get( 'id' ), 'course-category' ); 
		foreach( $terms as $term ) {
			$categories[ $term->term_id ] = $term->name;
		}

		return $categories;
	}

	/**
	 * Gets the list of students
	 * 
	 * @return [int|obj] array of the assignment IDs $user_id|$user
	 */
	public function get_students() {
		global $wpdb;

		$db_prefix = coschool_db_prefix();

		$students = $wpdb->get_results( $wpdb->prepare( "SELECT `student` FROM `{$wpdb->prefix}{$db_prefix}enrollments` WHERE `status` != 'pending' AND `course_id`  = %d", $this->get( 'id' ) ) );

		return $students;
	}

	/**
	 * URL to purchase a course
	 * 
	 * @since 0.9
	 * 
	 * @return string the URL
	 */
	public function get_purchase_url() {

		// If student has this course already purchased, return 'read' link instead
		if( $this->student->has_course( $this->get( 'id' ) ) ) {
			return get_permalink( $this->student->get_last_content( $this->get( 'id' ) ) );
		}

		$pricing    = $this->get( 'coschool_pricing' );
		$key		= 'enroll';
		// $key		= $this->get_type() == 'free' ? 'access' : 'enroll';

		$enroll_url = add_query_arg( $key, $this->get( 'id' ), trailingslashit( coschool_enroll_page( true ) ) );

		return apply_filters( 'coschool_enroll_url', $enroll_url, $pricing, $this );
	}

	public function get_enroll_label() {
		if( $this->student->has_course( $this->get( 'id' ) ) ) {
			return __( 'Continue Reading', 'coschool' );
		}
		elseif( 'free' == $this->get_type() ) {
			return __( 'Enroll for Free', 'coschool' );
		}
		else {
			return __( 'Enroll Today', 'coschool' );
		}
	}

	/**
	 * Gets student submitted reviews on this course
	 * 
	 * @since 0.9
	 * 
	 * @return [ obj ]
	 */
	public function get_reviews() {
		$comments = get_comments( [ 'post_id' => $this->get( 'id' ) ] );

		$reviews = [];
		foreach ( $comments as $comment ) {
			$rating 	= get_comment_meta( $comment->comment_ID, 'rating', true ) ? : 0;

			$reviews[] 	= [
				'id'				=> $comment->comment_ID,
				'course_id'			=> $comment->comment_post_ID,
				'comment_parent'	=> $comment->comment_parent,
				'reviewer_id'		=> $comment->user_id,
				'reviewer_name'		=> $comment->comment_author,
				'reviewer_email'	=> $comment->comment_author_email,
				'content'			=> $comment->comment_content,
				'rating'			=> $rating,
				'time'				=> strtotime( $comment->comment_date ),
			];
		}

		return $reviews;
	}

	/**
	 * Calculated average rating from all the reviews
	 * 
	 * @return float|int
	 */
	public function get_rating() {
		$rating = $review_count = 0;

		foreach ( $this->get_reviews() as $review ) {
			
			if ( $review['rating']  > 0 ) {
				$rating += $review['rating'];
				$review_count++;
			}
		}

		if( 0 == $review_count ) return 0;

		return round( $rating / $review_count, 2 );
	}

	/**
	 * Total amount earned by this course
	 *
	 * @return int|float the earning
	 */
	public function get_earnings() {
		global $wpdb;

		$db_prefix = coschool_db_prefix();

		$sales = $wpdb->get_row( $wpdb->prepare( "SELECT SUM(`price`) AS `earning` FROM `{$wpdb->prefix}{$db_prefix}enrollments` WHERE `status` != 'pending' AND `course_id` = %d", $this->get( 'id' ) ) );

		return is_null( $sales->earning ) ? 0 : $sales->earning;
	}

	/**
	 * Total time duration this course
	 *
	 * @return int|float
	 */
	public function get_duration() {

		$duration = $this->get( 'coschool_advanced' );
		
		return isset( $duration['course_duration'] ) ? $duration['course_duration'] : '';
	}
	/**
	 * If a user has access to this course
	 * 
	 * @param int $user_id The user/visitor ID
	 * 
	 * @return bool
	 */
	public function has_access( $user_id = null ) {

		if( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		
		return $this->student->has_course( $this->get( 'id' ) ) || user_can( $user_id, 'edit_post', $this->get( 'id' ) );
	}


	/**
	 * If this course has a certificate associated
	 * 
	 * @return bool
	 */
	public function has_certificate() {
		if( ! coschool_certificate_enabled() ) return false;

		$certification = $this->get( 'coschool_certification' );
		return isset( $certification['enable_certificate'] ) && 'yes' == $certification['enable_certificate'];
	}

	public function get_certificate_id() {
		$certification = $this->get( 'coschool_certification' );
		return isset( $certification['certificate'] ) ? $certification['certificate'] : 0;
		
	}

	public function get_certificate() {
		if( ! coschool_certificate_enabled() || ! $this->has_certificate() ) {
			return '';
		}

		elseif( coschool_instructor_can_edit_certificate() && 0 != ( $certificate_id = $this->get( 'certificate_id' ) ) ) {
			$certificate_data	= new Certificate_Data( $certificate_id );
			return $certificate_data->get( '_certificate_html' );
		}

		else {
			return stripslashes( get_option( '_certificate_html' ) );
		}
	}

    /**
     * If this course is completed by the given student
     * 
     * @param int $student_id
     * 
     * @since 0.9
     * 
     * @return bool
     */
    public function is_completed( $student_id ) {
    	$completed = true;
        foreach ( $this->get_contents() as $content_id ) {
        	if( ! $this->student->has_completed( $content_id ) ) {
        		$completed = false;
        		break;
        	}
        }

        return $completed;
    }
}