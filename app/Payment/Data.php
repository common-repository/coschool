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
class Data {

    /**
     * @var obj
     */
    public $method;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $method the method
     */
    public function __construct( $method ) {
        $this->method = $method;
        // parent::__construct( $this->method );
    }
}