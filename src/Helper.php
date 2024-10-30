<?php
/**
 * All helpers functions
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;
use Codexpert\Plugin\License;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Helper
 * @author Codexpert <hi@codexpert.io>
 */
class Helper extends Base {

	public static function pri( $data, $hide_adminbar = true ) {
		echo '<pre>';
		if( is_object( $data ) || is_array( $data ) ) {
			print_r( $data );
		}
		else {
			var_dump( $data );
		}
		echo '</pre>';

		if( $hide_adminbar ) {
			echo '<style>#adminmenumain{display:none;}</style>';
		}
	}

	/**
	 * @param bool $show_cached either to use a cached list of posts or not. If enabled, make sure to wp_cache_delete() with the `save_post` hook
	 */
	public static function get_posts( $args = [], $show_heading = false, $show_cached = false, $details = false ) {

		$defaults = [
			'post_type'         => 'post',
			'posts_per_page'    => -1,
			'post_status'		=> 'publish'
		];

		$_args = wp_parse_args( $args, $defaults );

		// use cache
		if( true === $show_cached && ( $cached_posts = wp_cache_get( "coschool_{$_args['post_type']}", 'coschool' ) ) ) {
			$posts = $cached_posts;
		}

		// don't use cache
		else {
			$queried = new \WP_Query( $_args );

			// if the raw list is required
			if ( $details ) {
				wp_cache_add( "coschool_{$_args['post_type']}", $queried->posts, 'coschool', 3600 );
				$posts = $queried->posts;	
			}

			// use a better formatted one
			else {
				$posts = [];
				foreach( $queried->posts as $post ) :
					$posts[ $post->ID ] = $post->post_title;
				endforeach;
			}
			
			// store in the cache
			wp_cache_add( "coschool_{$_args['post_type']}", $posts, 'coschool', 3600 );
		}

		$posts = $show_heading ? [ '' => sprintf( __( '- Choose a %s -', 'coschool' ), $_args['post_type'] ) ] + $posts : $posts;

		return apply_filters( 'coschool_get_posts', $posts, $_args );
	}

	/**
	 * Gets an associative array of terms from the given taxonomy
	 * 
	 * @since 0.9
	 * 
	 * @return [ $term_id => $name ]
	 */
	public static function get_terms( $args = [] ) {

		$args = wp_parse_args( $args, [ 'taxonomy' => 'course-category', 'hide_empty' => false ] );

		$terms = [];
		$_terms = get_terms( $args );
		foreach ( $_terms as $_term ) {
			$terms[ $_term->term_id ] = $_term->name;
		}

		return $terms;
	}

	/**
	 * Gets an associative array of users
	 * 
	 * @since 0.9
	 * 
	 * @return [ $term_id => $name ]
	 */
	public static function get_users( $args = [] ) {

		$users = [];
		$_users = get_users( $args );
		foreach ( $_users as $_user ) {
			$users[ $_user->ID ] = $_user->display_name;
		}

		return $users;
	}

	public static function get_option( $key, $section, $default = '', $repeater = false ) {

		$options = get_option( $key );

		if ( isset( $options[ $section ] ) ) {
			$option = $options[ $section ];

			if( $repeater === true ) {
				$_option = [];
				foreach ( $option as $key => $values ) {
					$index = 0;
					foreach ( $values as $value ) {
						$_option[ $index ][ $key ] = $value;
						$index++;
					}
				}

				return $_option;
			}
			
			return $option;
		}

		return $default;
	}

	/**
	 * @return string|false File path
	 */
	public static function locate_template( $template ) {
		$theme_path		= untrailingslashit( get_stylesheet_directory() ) . '/coschool';
		$plugin_path	= COSCHOOL_DIR . '/templates';

		if( file_exists( "{$theme_path}/{$template}" ) ) {
			return "{$theme_path}/{$template}";
		}
		elseif( file_exists( "{$plugin_path}/{$template}" ) ) {
			return "{$plugin_path}/{$template}";
		}

		return false;
	}

	/**
	 * Includes a template file resides in /views diretory
	 *
	 * It'll look into /coschool directory of your active theme
	 * first. if not found, default template will be used.
	 * can be overwriten with coschool_template_overwrite_dir hook
	 *
	 * @param string $slug slug of template. Ex: template-slug.php
	 * @param string $sub_dir sub-directory under base directory
	 * @param array $fields fields of the form
	 */
	public static function get_view( $slug, $base = 'views', $args = null ) {

		// templates can be placed in this directory
		$overwrite_template_dir = apply_filters( 'coschool_template_overwrite_dir', get_stylesheet_directory() . '/coschool/', $slug, $base, $args );
		
		// default template directory
		$plugin_template_dir 	= dirname( COSCHOOL ) . "/{$base}/";

		// full path of a template file in plugin directory
		$plugin_template_path 	=  $plugin_template_dir . $slug . '.php';
		
		// full path of a template file in overwrite directory
		$overwrite_template_path =  $overwrite_template_dir . $slug . '.php';

		// if template is found in overwrite directory
		if( file_exists( $overwrite_template_path ) ) {
			ob_start();
			include $overwrite_template_path;
			return ob_get_clean();
		}
		// otherwise use default one
		elseif ( file_exists( $plugin_template_path ) ) {
			ob_start();
			include $plugin_template_path;
			return ob_get_clean();
		}
		else {
			return __( 'Template not found!', 'coschool' );
		}
	}

	// /**
	//  * @return string|false File path
	//  */
	// public static function locate_module_template( $module, $template ) {
	// 	$theme_path		= untrailingslashit( get_stylesheet_directory() ) . '/coschool';
	// 	$plugin_path	= COSCHOOL_DIR . "/modules/{$module}/views/templates";

	// 	if( file_exists( "{$theme_path}/{$template}" ) ) {
	// 		return "{$theme_path}/{$template}";
	// 	}
	// 	elseif( file_exists( "{$plugin_path}/{$template}" ) ) {
	// 		return "{$plugin_path}/{$template}";
	// 	}

	// 	return false;
	// }
}