<?php
/**
 * An abstraction for the Payment
 */
namespace Codexpert\CoSchool\Abstracts;

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
class Payment_Method {

	/**
	 * @var obj $wpdb
	 */
	public $database;

	/**
	 * Constructor function
	 * 
	 * @uses WP_Post class
	 * @param int|obj $method the method
	 */
	public function __construct() {
		$this->database = new DB;
	}

	public function enqueue_scripts() {}

	public function payment_form() {}

	/**
	 * Let's process the payment
	 * 
	 * @param array $posted The form data
	 */
	public function payment_id( $payment_id, $posted ) {}
}