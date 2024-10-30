<?php
/**
 * All course related functions
 */
namespace Codexpert\CoSchool\App;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Question
 * @author Codexpert <hi@codexpert.io>
 */
class Question {

	public $plugin;
	
	/**
	 * Plugin instance
	 * 
	 * @access private
	 * 
	 * @var Plugin
	 */
	private static $_instance;

	/**
	 * Constructor function
	 */
	public function __construct() {
		add_action( 'init', [ new Question\Post_Type, 'register' ] );
		add_action( 'manage_question_posts_columns', [ new Question\Post_Type, 'add_table_columns' ] );
		add_action( 'manage_question_posts_custom_column', [ new Question\Post_Type, 'add_column_content' ], 10, 2 );

		add_action( 'add_meta_boxes', [ new Question\Meta, 'config' ], 11 );
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