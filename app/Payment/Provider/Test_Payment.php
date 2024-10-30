<?php
/**
 * An abstraction for the Payment
 */
namespace Codexpert\CoSchool\App\Payment\Provider;
use Codexpert\CoSchool\Abstracts\Payment_Method;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Payment_Method
 * @author Codexpert <hi@codexpert.io>
 */
class Test_Payment extends Payment_Method {

	/**
	 * Constructor function
	 * 
	 * @uses WP_Post class
	 * @param int|obj $method the method
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Let's process the payment
	 * 
	 * @param array $posted The form data
	 */
	public function payment_id( $payment_id, $posted ) {
		return -1;
	}
}