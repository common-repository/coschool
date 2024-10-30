<?php
namespace Codexpert\CoSchool\App\CourseBundle;
use Codexpert\CoSchool\Abstracts\Post_Data;
use Codexpert\CoSchool\Abstracts\DB;
use Codexpert\CoSchool\App\Course\Data as Course_Data;
use Codexpert\CoSchool\App\Student\Data as Student_Data;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * @package Plugin
 * @subpackage Bundle
 * @author Codexpert <hi@codexpert.io>
 */
class Data extends Post_Data {

    /**
     * @var obj
     */
    public $bundle;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $bundle the bundle
     */
    public function __construct( $bundle ) {
        $this->bundle   = get_post( $bundle );
        parent::__construct( $this->bundle );
    }

     /**
     * Gets associated course List
     * 
     * @return int|obj the course ID $post_id|$post
     */
    public function get_courses() {
        $bundle_pricing = $this->get( 'bundle_pricing' );
        return isset( $bundle_pricing['select_course'] ) ? $bundle_pricing['select_course'] : [];
    }

    /**
     * Gets Bundle Price 
     * 
     * @return int|obj the course ID $post_id|$post
     */
    public function get_price() {
        $bundle_pricing = $this->get( 'bundle_pricing' );
        return isset( $bundle_pricing['price'] ) && $bundle_pricing['price'] != '' ? $bundle_pricing['price'] : 0;
    } 
}