<?php
/**
 * The core application
 * 
 * @folder /coschool/app
 */
namespace Codexpert\CoSchool;
use Codexpert\Plugin\Base;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage App
 * @author Codexpert <hi@codexpert.io>
 */
class App extends Base {

	public $plugin;

	public $slug;
	
	public $name;

	public $server;

	public $version;

	public $modules;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];
		
		$this->modules = [ 
			__( 'Course', 'coschool' ), 
			__( 'Lesson', 'coschool' ), 
			__( 'Quiz', 'coschool' ), 
			__( 'Question', 'coschool' ), 
			__( 'Coupon', 'coschool' ), 
			__( 'Instructor', 'coschool' ), 
			__( 'Student', 'coschool' ), 
			__( 'Enrollment', 'coschool' ), 
			__( 'Payment', 'coschool' ), 
			__( 'Report', 'coschool' ),
			__( 'Assignment', 'coschool' ),
			__( 'CourseBundle', 'coschool' ),  
			__( 'Smtp', 'coschool' ),  
		];
	}

	public function load() {

		foreach ( $this->modules as $module ) {
			$class_name = 'Codexpert\CoSchool\App\\'. $module;
			if( class_exists( $class_name ) ) {
				$class_name::instance();
			}
		}
	}
}