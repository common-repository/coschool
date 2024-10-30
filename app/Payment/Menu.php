<?php
/**
 * All lesson related functions
 */
namespace Codexpert\CoSchool\App\Payment;
use Codexpert\CoSchool\Helper;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Payment
 * @author Codexpert <hi@codexpert.io>
 */
class Menu {

	public function register() {
		add_submenu_page( 'coschool', __( 'Payments', 'coschool' ), __( 'Payments', 'coschool' ), 'manage_options', 'payments', [ $this, 'callback_reports' ] );
	}

    public function callback_reports() {
        echo Helper::get_view( 'payments', 'views/adminmenu' );
    }
}