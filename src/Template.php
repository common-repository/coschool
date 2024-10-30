<?php
/**
 * All template facing functions
 * 
 * @folder /templates
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Template
 * @author Codexpert <hi@codexpert.io>
 */
class Template extends Base {

	public $plugin;

	public $slug;
	
	public $name;

	public $server;

	public $version;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];
	}

	public function init() {
		
		/**
		 * Overwrite the template files
		 * 
		 * @since 0.9
		 */

		// archive template
		$this->filter( 'archive_template', 'archive_template' );
		$this->filter( 'author_template', 'archive_template' );

		// single template
		$this->filter( 'single_template', 'single_template' );

		// alter native author template
		$this->action( 'pre_get_posts', 'pre_get_posts' );

		// wrapper div for CoSchool contents
		$this->action( 'coschool_before_main_content', 'before_main_content' );
		$this->action( 'coschool_after_main_content', 'after_main_content' );

		/**
		 * Render template content
		 * 
		 * @since 0.9
		 */

		// course archive
		$this->action( 'coschool_archive_description', 'header_archive_course' );
		$this->action( 'coschool_before_courses_loop', 'toggle_layout_archive_course' );
		$this->action( 'coschool_courses_loop', 'content_archive_course' );
		$this->action( 'coschool_after_courses_loop', 'pagination_archive_course' );
		
		// single course
		$this->action( 'coschool_single_course', 'content_single_course' );
		$this->action( 'coschool_course_tab_content_description', 'course_content_description_tab' );
		$this->action( 'coschool_course_tab_content_reviews', 'course_content_reviews_tab' );
		$this->action( 'coschool_course_tab_content_notice', 'course_content_notice_tab' );
		$this->action( 'coschool_course_tab_content_faq', 'course_content_faq_tab' );
		$this->action( 'coschool_lesson_tab_content_content', 'lesson_tab_content_content' );
		$this->action( 'coschool_lesson_tab_content_discussion', 'lesson_tab_content_discussion' );
		$this->action( 'coschool_course_progress', 'course_progress' );

		// single lesson
		$this->action( 'coschool_single_lesson', 'content_single_lesson' );

		// single quiz
		$this->action( 'coschool_single_quiz', 'content_single_quiz' );

		// single assignment
		$this->action( 'coschool_single_assignment', 'content_single_assignment' );
		$this->action( 'coschool_prerequisites', 'content_prerequisites', 10, 2 );

		// native enroll page
		$this->action( 'coschool_enroll_cart', 'enroll_cart' );
		$this->action( 'coschool_enroll_payment_form', 'payment_form' );
		$this->action( 'coschool_enroll_before_payment_methods', 'student_info' );

		//student dashboard
		$this->action( 'coschool_student_dashboard_navbar', 'student_dashboard_navbar' );
		$this->action( 'coschool_student_dashboard_navbar', 'user_switcher' );
		$this->action( 'coschool_student_dashboard_content_summary', 'student_dashboard_summary' );
		$this->action( 'coschool_student_dashboard_content_courses', 'student_dashboard_enrolled_courses' );
		$this->action( 'coschool_student_dashboard_content_my-profile', 'student_dashboard_my_profile' );
		$this->action( 'coschool_student_dashboard_content_wishlist', 'student_dashboard_wishlist' );
		$this->action( 'coschool_student_dashboard_content_transaction', 'student_dashboard_transaction' );
	}

	public function archive_template( $_template ) {
		global $post;

		if ( is_archive() && get_post_type( $post ) == 'course' && false !== ( $template = Helper::locate_template( 'archive-course.php' ) ) ) {
			return $template;
		}

		return $_template;
	}

	public function single_template( $_template ) {
		global $post;

		if ( is_singular( 'course' ) && get_post_type( $post ) == 'course' && false !== ( $template = Helper::locate_template( 'single-course.php' ) ) ) {
			return $template;
		}

		elseif ( is_singular( 'lesson' ) && get_post_type( $post ) == 'lesson' && false !== ( $template = Helper::locate_template( 'single-lesson.php' ) ) ) {
			return $template;
		}

		elseif ( is_singular( 'quiz' ) && get_post_type( $post ) == 'quiz' && false !== ( $template = Helper::locate_template( 'single-quiz.php' ) ) ) {
			return $template;
		}

		elseif ( is_singular( 'assignment' ) && get_post_type( $post ) == 'assignment' && false !== ( $template = Helper::locate_template( 'single-assignment.php' ) ) ) {
			return $template;
		}

		return $_template;
	}

	public function pre_get_posts( $query ) {

		if ( ! $query->is_main_query() ) {
		    return;
		}

		if ( is_admin() && ! current_user_can( 'edit_pages' ) ) {
		    $query->set( 'author', get_current_user_id() );
		}

		if ( ! is_admin() && is_author() ) {
		    $query->set( 'post_type', [ 'course' ] );
		}
	}

	public function before_main_content() {
		echo "<div class='" . implode( ' ', apply_filters( 'coschool_wrapper_classes', [ 'coschool-container' ] ) ) . "'>";
	}

	public function after_main_content() {
		echo "</div>";
	}

	public function header_archive_course() {
		if ( false !== ( $template = Helper::locate_template( 'header-archive-course.php' ) ) ) {
			include $template;
		}
	}

	public function toggle_layout_archive_course() {
		?>
		<div class="course-layout-toggle-btn">
			<i class="fas fa-th"></i>
			<i class="fas fa-th-list" style="display: none;"></i>
		</div>
		<?php
	}

	public function content_archive_course() {
		if ( false !== ( $template = Helper::locate_template( 'content-archive-course.php' ) ) ) {
			include $template;
		}
	}

	public function pagination_archive_course() {
		if ( false !== ( $template = Helper::locate_template( 'pagination-archive-course.php' ) ) ) {
			include $template;
		}
	}

	public function content_single_course() {
		if ( false !== ( $template = Helper::locate_template( 'content-single-course.php' ) ) ) {
			include $template;
		}
	}

	public function course_content_description_tab() {
		if ( false !== ( $template = Helper::locate_template( 'course/tabs/description.php' ) ) ) {
			include $template;
		}
	}

	public function course_content_reviews_tab() {
		if ( false !== ( $template = Helper::locate_template( 'course/tabs/reviews.php' ) ) ) {
			include $template;
		}
	}

	public function course_content_notice_tab() {
		if ( false !== ( $template = Helper::locate_template( 'course/tabs/notice.php' ) ) ) {
			include $template;
		}
	}

	public function course_content_faq_tab() {
		if ( false !== ( $template = Helper::locate_template( 'course/tabs/faq.php' ) ) ) {
			include $template;
		}
	}

	public function lesson_tab_content_content() {
		if ( false !== ( $template = Helper::locate_template( 'lesson/tabs/content.php' ) ) ) {
			include $template;
		}
	}

	public function lesson_tab_content_discussion() {
		if ( false !== ( $template = Helper::locate_template( 'lesson/tabs/discussion.php' ) ) ) {
			include $template;
		}
	}

	public function course_progress( $courses_id ) {
		if ( false !== ( $template = Helper::locate_template( 'course/course-progress.php' ) ) ) {
			include $template;
		}
	}

	public function content_single_lesson() {
		if ( false !== ( $template = Helper::locate_template( 'content-single-lesson.php' ) ) ) {
			include $template;
		}
	}

	public function content_single_quiz() {
		if ( false !== ( $template = Helper::locate_template( 'content-single-quiz.php' ) ) ) {
			include $template;
		}
	}

	public function content_prerequisites( $prerequisites, $data ) {
		if ( false !== ( $template = Helper::locate_template( 'prerequisites.php' ) ) ) {
			include $template;
		}
	}

	public function enroll_cart() {
		echo Helper::get_view( 'cart', 'templates/shortcodes/enroll' );
	}

	public function student_info() {
		echo Helper::get_view( 'student-info', 'templates/shortcodes/enroll' );
	}

	public function payment_form() {
		echo Helper::get_view( 'payment-form', 'templates/shortcodes/enroll' );
	}

	public function user_switcher() {
		
		if ( ! function_exists('coschool_instructor_dashboard_nav_items') ) return;
		
		// if the user don't have both the caps, exit
		if( ! current_user_can( 'read_courses' ) || ! current_user_can( 'create_courses' ) ) return;

		$user_type = ! in_array( ( $user_type = get_user_meta( get_current_user_id(), '_viewing_as', true ) ), [ 'instructor', 'student' ] ) ? 'instructor' : $user_type;

		$view_as = $user_type != 'instructor' ? __( 'Instructor', 'coschool' ) : __( 'Student', 'coschool' );

		echo '<div class="coschool-user-switch-panel">';
		echo '<button id="coschool-user-switch">' . sprintf( __( 'View as %s', 'coschool' ), $view_as ) . '</button>';
		echo '</div>';
	}

	public function student_dashboard_navbar() {
		echo Helper::get_view( 'nav-bar', 'templates/shortcodes/student-dashboard' );
	}

	public function student_dashboard_summary() {
		echo Helper::get_view( 'summary', 'templates/shortcodes/student-dashboard/contents' );
	}

	public function student_dashboard_enrolled_courses() {
		echo Helper::get_view( 'courses', 'templates/shortcodes/student-dashboard/contents' );
	}

	public function student_dashboard_my_profile() {
		echo Helper::get_view( 'my-profile', 'templates/shortcodes/student-dashboard/contents' );
	}

	public function student_dashboard_wishlist() {
		echo Helper::get_view( 'wishlist', 'templates/shortcodes/student-dashboard/contents' );
	}
	
	public function student_dashboard_transaction() {
		echo Helper::get_view( 'transaction', 'templates/shortcodes/student-dashboard/contents' );
	}
	public function content_single_assignment() {
		echo Helper::get_view( 'content-single-assignment', 'templates/assignment' );
	}

}