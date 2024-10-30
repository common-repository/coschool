<?php
/**
 * An abstraction for the Post_Data
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
 * @subpackage Post_Data
 * @author Codexpert <hi@codexpert.io>
 */
abstract class Post_Data {

    /**
     * @var obj
     */
    public $post;

    /**
     * List of keys/phrases for alternative using
     * 
     * @var obj
     */
    public $correction;

    /**
     * Constructor function
     * 
     * @uses WP_Post class
     * @param int|obj $post the post
     */
    public function __construct( $post ) {

        $this->post = get_post( $post );

        $this->correction = apply_filters( 'coschool_post-data-correction', [
            'id'            => 'ID',
            'title'         => 'post_title',
            'name'          => 'post_title',
            'content'       => 'post_content',
            'author'        => 'post_author',
            'instructor'    => 'post_author',
            'date'          => 'post_date',
            'excerpt'       => 'post_excerpt',
            'status'        => 'post_status',
            'updated'       => 'post_modified',
        ], $this->post );
    }

    /**
     * Gets post data
     * 
     * @param string $key the key
     * @param string $default default value
     * 
     * @uses get_post_meta()
     * @uses WP_Post class
     * 
     * @return mix|null the post data if found, null otherwise
     */
    public function get( $key, $default = null ) {

        // if we have a dedicated method written for this
        if( method_exists( $this, ( $method = "get_{$key}" ) ) ) {
            return $this->$method();
        }

        // get the correct key
        if( array_key_exists( $key, $this->correction ) ) {
            $key = $this->correction[ $key ];
        }

        // if it's a post `data`
        if( isset( $this->post->$key ) && $this->post->$key != '' ) {
            return $this->post->$key;
        }

        return $default;
    }

    /**
     * Sets a data
     * 
     * @param string $key the key
     * @param mix $value the value for the given key
     * 
     * @return void
     */
    public function set( $key, $value ) {

        // we shouldn't allow to update the ID, should we?
        if( strtolower( $key ) == 'id' ) return;

        // get the correct key
        if( array_key_exists( $key, $this->correction ) ) {
            $key = $this->correction[ $key ];
        }

        // if it's a post `data`
        if( in_array( $key, array_values( $this->correction ) ) ) {
			wp_update_post( [
				'ID'	=> $this->post->ID,
				$key	=> $value
			] );
        }

        // post meta
        else {
            update_post_meta( $this->post->ID, $key, $value );
        }
    }

    /**
     * Sets a data
     * 
     * @param string $key the key
     * @param mix $value the value for the given key
     * 
     * @return void
     */
    public function unset( $key ) {

        // get the correct key
        if( array_key_exists( $key, $this->correction ) ) {
            $key = $this->correction[ $key ];
        }

        // if it's a post `meta data`
        delete_post_meta( $this->post->ID, $key );
    }

    /**
     * Is this content visible by the given user?
     * 
     * @return bool
     */
    public function is_visible_by() {
        return true;
    }

    /**
     * If this lesson has prerequisites
     * 
     * @param int $student_id the student id
     * @param string $content lesson|quiz|assignment
     * 
     * @return array
     */
    public function get_prerequisites( $content = 'all' ) {

        $prerequisites  = [];
        $post_type      = $this->get( 'post_type' );

        if ( ! in_array( $post_type, [ 'lesson', 'quiz', 'assignment' ] ) ) return $prerequisites;

        $meta_key = "coschool_{$post_type}_config";

        if ( 'quiz' == $post_type ) $meta_key = "coschool_{$post_type}_prerequisites";

        $config = $this->get( $meta_key );

        if ( $content == 'all' || $content == 'lesson' ) {
            $lesson         = isset( $config['prerequisites_lesson'] ) ? $config['prerequisites_lesson'] : [];
            $prerequisites  = array_merge( $prerequisites, $lesson );
        }
        if ( $content == 'all' || $content == 'quiz' ) {
            $quiz           = isset( $config['prerequisites_quiz'] ) ? $config['prerequisites_quiz'] : [];
            $prerequisites  = array_merge( $prerequisites, $quiz );
        }
        if ( $content == 'all' || $content == 'assignment' ) {
            $assignment     = isset( $config['prerequisites_assignment'] ) ? $config['prerequisites_assignment'] : [];
            $prerequisites  = array_merge( $prerequisites, $assignment );
        }

        return $prerequisites;
    }

    /**
     * Gets the URL
     * 
     * @return string
     */
    public function get_url() {
        return get_permalink( $this->post->ID );
    }
}