<?php
namespace Codexpert\CoSchool\App;

class Smtp {
	
	/**
	 * Plugin instance
	 * 
	 * @access private
	 * 
	 * @var Plugin
	 */
	private static $_instance;

	public function __construct() {
	
		/**
		 * Settings
		 */
		add_filter( 'coschool_settings', [ new Smtp\Settings, 'smtp_settings' ] );
		
    	/**
		 * Email
		 */
		add_action( 'plugins_loaded', [ new Smtp\Email, 'init' ] );
	}

	/**
	 * Instantiate the plugin
	 * 
	 * @access public
	 * 
	 * @return $_instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}