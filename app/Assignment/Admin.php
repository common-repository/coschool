<?php
/**
 * All admin facing functions
 */
namespace Codexpert\CoSchool\App\Assignment;

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
class Admin {

	/**
	 * Constructor function
	 */
	public function __construct() {
	}

	public function content_tabs( $tabs ) {
		$tabs[] = '<button class="course-content-btn" data-type="assignment">'. __( 'Add Assignment', 'coschool' ) .'</button>';
		return $tabs;
	}
}