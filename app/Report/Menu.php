<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Report;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Report
 * @author Codexpert <hi@codexpert.io>
 */
class Menu {

	public function register() {
		add_submenu_page( 'coschool', __( 'Reports', 'coschool' ), __( 'Reports', 'coschool' ), 'manage_options', 'reports', [ $this, 'callback_reports' ] );
	}

    public function callback_reports() {
        echo Helper::get_view( 'reports', 'views/adminmenu' );
    }
	
}