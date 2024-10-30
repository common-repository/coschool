<?php
/**
 * An abstraction for the User_Data
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
 * @subpackage User_Data
 * @author Codexpert <hi@codexpert.io>
 */
abstract class User_Data {

    /**
     * @var obj
     */
    public $user;

    /**
     * List of keys/phrases for alternative using
     * 
     * @var obj
     */
    public $correction;

    /**
     * Constructor function
     * 
     * @uses WP_User class
     * @param int|obj $user the user
     */
    public function __construct( $user ) {
        $this->user = $user;
        
        if( ! is_a( $this->user, 'WP_User' ) ) {
            $this->user = new \WP_User( $this->user );
        }

        $this->correction = apply_filters( 'coschool_user-data-correction', [
            'id'            => 'ID',
            'email'         => 'user_email',
            'username'      => 'user_login',
            'firstname'     => 'user_firstname',
            'lastname'      => 'user_lastname',
            'registered'    => 'user_registered',
            'joined'        => 'user_registered',
            'name'          => 'display_name',
        ], $this->user );
    }

    /**
     * Gets user data
     * 
     * @param string $key the key
     * 
     * @uses get_user_meta()
     * @uses WP_User class
     * 
     * @return mix|null the user data if found, null otherwise
     */
    public function get( $key ) {

        if( method_exists( $this, ( $method = "get_{$key}" ) ) ) {
            return $this->$method();
        }

        if( array_key_exists( $key, $this->correction ) ) {
            $key = $this->correction[ $key ];
        }

        if( isset( $this->user->$key ) ) {
            return $this->user->$key;
        }
        elseif( $meta = get_user_meta( $this->user->ID, $key, true ) ) {
            return $meta;
        }

        return null;
    }
    
    /**
     * Sets a data
     * 
     * @param string $key the key
     * @param mix $value the value for the given key
     * 
     * @return void
     */
    public function set( $key, $value, $type = 'text' ) {

        // we shouldn't allow to update the ID, should we?
        if( strtolower( $key ) == 'id' ) return;

        // get the correct key
        if( array_key_exists( $key, $this->correction ) ) {
            $key = $this->correction[ $key ];
        }

        // if it's a user `data`
        if( in_array( $key, array_values( $this->correction ) ) ) {
            wp_update_user( [
                'ID'    => $this->user->ID,
                $key    => coschool_sanitize( $value, $type )
            ] );
        }

        // user meta
        else {
            update_user_meta( $this->user->ID, $key, coschool_sanitize( $value, $type ) );
        }
    }

    /**
     * Gets the URL
     * 
     * @return string
     */
    public function get_url() {
        return get_author_posts_url( $this->get( 'id' ) );
    }

    /**
     * User's avatar URL
     * 
     * @return string
     */
    public function get_avatar_url() {
        return get_avatar_url( $this->get( 'id' ) );
    }
}