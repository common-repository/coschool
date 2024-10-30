<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\CoSchoolPro;
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
		add_submenu_page( 'coschool', __( 'CoSchool Pro', 'coschool' ), __( 'Pro Features', 'coschool' ), 'manage_options', 'coschool-pro', [ $this, 'callback_pro_menu' ] );
	}

    public function callback_pro_menu() {
        if ( defined( 'COSCHOOL_PRO' ) ) {
            echo Helper::get_view( 'coschool-pro-licence', 'views/coschool-pro' );
        }
        else{
            echo Helper::get_view( 'coschool-pro-feature', 'views/coschool-pro' );
        }
    }
	
}