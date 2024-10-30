<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Instructor;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Instructor
 * @author Codexpert <hi@codexpert.io>
 */
class User_Role {

	public function register() {
		
		global $wp_roles;

		if ( ! isset( $wp_roles ) )
		$wp_roles = new \WP_Roles();

		$author = $wp_roles->get_role( 'author' );

		$wp_roles->add_role( 'instructor', __( 'Instructor', 'coschool' ), $author->capabilities );

		$instructor = get_role( 'instructor' );
		$instructor->add_cap( 'create_courses' );
		$instructor->add_cap( 'delete_posts' );
		$instructor->add_cap( 'edit_posts' );
	}
}