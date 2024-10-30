<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Student;
use Codexpert\CoSchool\Helper;

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
class Menu {

	public function register() {
		add_submenu_page( 'coschool', __( 'Students', 'coschool' ), __( 'Students', 'coschool' ), 'manage_options', 'students', [ $this, 'callback_students' ] );
	}

    public function callback_students() {
        echo Helper::get_view( 'students', 'views/adminmenu' );
    }	
}