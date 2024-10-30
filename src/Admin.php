<?php
/**
 * All admin facing functions
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;
use Codexpert\CoSchool\Abstracts\DB;
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
class Admin extends Base {

	public $plugin;

	public $slug;
	
	public $name;

	public $server;

	public $version;

	public $database;

	public $admin_url;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];

		$this->database = new DB();
	}

	/**
	 * Internationalization
	 */
	public function i18n() {
		load_plugin_textdomain( 'coschool', false, COSCHOOL_DIR . '/languages/' );
	}

	/**
	 * Installer. Runs once when the plugin in activated.
	 *
	 * @since 1.0
	 */
	public function install() {

		if( ! get_option( 'coschool_version' ) ){
			update_option( 'coschool_version', $this->version );
		}
		
		if( ! get_option( 'coschool_install_time' ) ){
			update_option( 'coschool_install_time', time() );
		}

		// install custom tables
		$this->database->create_tables();

		// create a folder in the uplaod directory
		$upload_dir = wp_upload_dir();
		wp_mkdir_p( trailingslashit( $upload_dir['basedir'] ) . 'coschool/' );
	}

	public function add_menu() {
		add_menu_page( __( 'CoSchool', 'coschool' ), __( 'CoSchool', 'coschool' ), coschool_menu_cap(), 'coschool', '', COSCHOOL_ASSET . '/img/icon-white.svg', 15 );
		add_submenu_page( 'coschool', __( 'Add-ons', 'coschool' ), __( 'Add-ons', 'coschool' ), 'manage_options', 'coschool-add-ons', [ $this, 'callback_addons' ], 99 );
	}

	public function callback_addons( $args ) {
		echo Helper::get_view( 'add-ons', 'views/adminmenu' );
	}

	/**
	 * Changes CoSchool submenu items
	 */
	public function menu_position() {
		global $submenu;
		$coschool_submenus = $submenu['coschool'];

		$moving_items = [ 'coschool-add-ons', 'coschool' ];
		$temp_list = [];

		foreach ( $coschool_submenus as $index => $menu_item ) {
			if( array_intersect( $moving_items, $menu_item ) ) {
				$temp_list[] = $menu_item;
				unset( $coschool_submenus[ $index ] );
			}
		}

		// return
		$submenu['coschool'] = array_merge( $coschool_submenus, $temp_list );
	}

	public function add_admin_bar_menu() {
		global $wp_admin_bar;
		$wp_admin_bar->add_node(
			[
				'id'		=> 'courses',
				'title'		=> __( 'View Courses', 'coschool' ),
				'href'		=> get_post_type_archive_link( 'course' ),
				'parent'	=> 'site-name',
				'meta'		=> [ 'class' => 'admin-bar-courses' ],
			]
		);
	}

	public function action_links( $links ) {
		$this->admin_url = admin_url( 'admin.php' );

		$new_links = [
			'settings'	=> sprintf( '<a href="%1$s">' . __( 'Settings', 'coschool' ) . '</a>', add_query_arg( 'page', $this->slug, $this->admin_url ) )
		];
		
		return array_merge( $new_links, $links );
	}

	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		
		if ( $this->plugin['basename'] === $plugin_file ) {
			$plugin_meta['help'] = '<a href="https://help.codexpert.io/" target="_blank" class="cx-help">' . __( 'Help', 'coschool' ) . '</a>';
		}

		return $plugin_meta;
	}

	public function update_cache( $post_id, $post, $update ) {
		wp_cache_delete( "coschool_{$post->post_type}", 'coschool' );
	}

	public function footer_text( $text ) {
		if( get_current_screen()->parent_base != $this->slug ) return $text;

		return sprintf( __( 'If you like <strong>%1$s</strong>, please <a href="%2$s" target="_blank">leave us a %3$s rating</a> on WordPress.org! It\'d motivate and inspire us to make the plugin even better!', 'coschool' ), $this->name, "https://wordpress.org/support/plugin/{$this->slug}/reviews/?filter=5#new-post", '⭐⭐⭐⭐⭐' );
	}

	public function settings_saved( $section ) {
		if ( $section == 'coschool_advanced' ) {
			flush_rewrite_rules( true );
		}
	}

	public function admin_footer() {
		echo '<div id="coschool-loader-container" style="display: none;"><div id="coschool-loader"></div></div>';
	}
	
	public function editor( $r ){
		return 'html';
	}

	public function reviews_comment( $columns ) {

		$date = $columns['date'];
		unset( $columns['date'] );
		$columns['rating'] 	= __( 'Rating', 'coschool' );
		$columns['date'] 	= $date;
		return $columns;
	}

	public function rating_column( $column, $comment_ID ) {

		$comment			= get_comment( $comment_ID );
		$comment_post_id	= $comment->comment_post_ID ;
		$course_data		= new Course_Data( $comment_post_id );

		if ( 'rating' == $column ) {
			if ( get_post_type( $comment_post_id ) == 'course' ) {
				// Escape the output of coschool_populate_stars()
				echo wp_kses_post( coschool_populate_stars( $course_data->get( 'rating' ) ) );
			} else {
				echo esc_html( "-" );
			}
		}
	}

	public function admin_notices() {
		
		if( ! current_user_can( 'manage_options' )  ) return;

		$notice_key = "_{$this->slug}_notices-dismissed";
		/**
		 * Promotional banners
		 */
		$banners = [

			// Regular promotion. Shows on 1st to 7th of every month.
			
			'holiday-deals'	=> [
				'name'	=> __( 'CoDesigner', 'coschool' ),
				'url'	=> 'http://codexpert.io/coupons',
				'type'	=> 'image',
				'image'	=>	COSCHOOL_ASSET.'/img/holiday-deals.png',
				'from'	=> strtotime( date( '2022-12-20 23:59:59' ) ),
				'to'	=> strtotime( date( '2023-01-07 23:59:59' ) ),
			],
			
		];

		if( isset( $_GET['is-dismiss'] ) && array_key_exists( $_GET['is-dismiss'], $banners ) ) {
			$dismissed = get_option( $notice_key ) ? : [];
			$dismissed[] = sanitize_text_field( $_GET['is-dismiss'] );
			update_option( $notice_key, array_unique( $dismissed ) );
		}

		$dismissed = get_option( $notice_key ) ? : [];
		$active_banners = array_values( array_diff( array_keys( $banners ), $dismissed ) );

		$rand_index = rand( 0, count( $active_banners ) - 1 );
		$rand_img = false;
		if( isset( $active_banners[ $rand_index ] ) ) {
			$rand_img = $active_banners[ $rand_index ];
		}

		if( $rand_img ) {
			$query_args = [ 'is-dismiss' => $rand_img ];

			if( count( $_GET ) > 0 ) {
				$query_args = array_map( 'sanitize_text_field', $_GET ) + $query_args;
			}

			if( isset( $banners[ $rand_img ]['from'] ) && $banners[ $rand_img ]['from'] > time() ) return;
			if( isset( $banners[ $rand_img ]['to'] ) && $banners[ $rand_img ]['to'] < time() ) return;

			?>
			<div class="notice notice-success cx-notice cx-shadow is-dismissible cx-promo cx-promo-<?php echo $banners[ $rand_img ]['type']; ?>">

				<?php if( 'image' == $banners[ $rand_img ]['type'] ) : ?>
				<a href="<?php echo add_query_arg( [ 'utm_campaign' => $rand_img ], $banners[ $rand_img ]['url'] ); ?>" target="_blank">
					<img id="<?php echo "promo-{$rand_img}"?>" src="<?php echo $banners[ $rand_img ]['image']; ?>">
				</a>
				<?php endif; ?>

				<?php if( 'text' == $banners[ $rand_img ]['type'] ) : ?>
				<a href="<?php echo add_query_arg( [ 'utm_campaign' => $rand_img ], $banners[ $rand_img ]['url'] ); ?>" target="_blank">
					<?php echo $banners[ $rand_img ]['text']; ?>
				</a>
				<?php endif; ?>

				<a href="<?php echo add_query_arg( $query_args, '' ); ?>" class="notice-dismiss"><span class="screen-reader-text"></span></a>
			</div>
			<?php
		}
	}
}