<?php
/**
 * All cron related functions
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
 * @subpackage Cron
 * @author Codexpert <hi@codexpert.io>
 */
class Cron extends Base {

	public $plugin;

	public $slug;
	
	public $name;

	public $server;

	public $version;

	/**
	 * Constructor function
	 */
	public function __construct( $plugin ) {
		$this->plugin	= $plugin;
		$this->slug		= $this->plugin['TextDomain'];
		$this->name		= $this->plugin['Name'];
		$this->server	= $this->plugin['server'];
		$this->version	= $this->plugin['Version'];
	}

	/**
	 * Installer. Runs once when the plugin in activated.
	 *
	 * @since 1.0
	 */
	public function install() {
		/**
		 * Schedule an event to sync help docs
		 */
		if ( ! wp_next_scheduled ( 'codexpert-daily' )) {
		    wp_schedule_event( time(), 'daily', 'codexpert-daily' );
		}
	}

	/**
	 * Uninstaller. Runs once when the plugin in deactivated.
	 *
	 * @since 1.0
	 */
	public function uninstall() {
		/**
		 * Remove scheduled hooks
		 */
		wp_clear_scheduled_hook( 'codexpert-daily' );
	}

	public function initial_calls() {
		
		if( 1 == get_option( 'coschool_initial_calls' ) ) return;
		
		// Sync docs for the first time
		if( get_option( 'coschool_docs_json' ) == '' ) {
			$this->daily();
		}
		
		update_option( 'coschool_initial_calls', 1 );
	}

	/**
	 * Daily events
	 */
	public function daily() {
		/**
		 * Sync blog posts from https://pluggable.io
		 *
		 * @since 1.0
		 */
	    $_posts = 'https://pluggable.io/wp-json/wp/v2/posts/';
	    if( ! is_wp_error( $_posts_data = wp_remote_get( $_posts ) ) ) {
	        update_option( 'pluggable-blog-json', json_decode( $_posts_data['body'], true ) );
	    }
		/**
		 * Sync docs from https://help.codexpert.io
		 *
		 * @since 1.0
		 */
	    if( isset( $this->plugin['doc_id'] ) && !is_wp_error( $_docs_data = wp_remote_get( "https://help.codexpert.io/wp-json/wp/v2/docs/?parent={$this->plugin['doc_id']}&per_page=20&orderby=menu_order&order=asc" ) ) ) {

	        update_option( 'coschool_docs_json', json_decode( $_docs_data['body'], true ) );
	    }
	    
	}
}