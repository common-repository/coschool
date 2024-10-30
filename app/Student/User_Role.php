<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Student;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Student
 * @author Codexpert <hi@codexpert.io>
 */
class User_Role {

	public function register() {
		
		global $wp_roles;

		if ( ! isset( $wp_roles ) )
		$wp_roles = new \WP_Roles();

		$contributor = $wp_roles->get_role( 'contributor' );

		$wp_roles->add_role( 'student', __( 'Student', 'coschool' ), $contributor->capabilities );

		$student = get_role( 'student' );
		$student->add_cap( 'read_courses' );
		$student->add_cap('upload_files');
	}
	
}