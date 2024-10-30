<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Enrollment;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Enrollment
 * @author Codexpert <hi@codexpert.io>
 */
class Menu {

	public function register() {
		add_submenu_page( 'coschool', __( 'Enrollments', 'coschool' ), __( 'Enrollments', 'coschool' ), 'manage_options', 'enrollment', [ $this, 'callback_enrollment' ] );
	}

    public function callback_enrollment() {
        echo Helper::get_view( 'enrollment', 'views/adminmenu' );
    }
	
}