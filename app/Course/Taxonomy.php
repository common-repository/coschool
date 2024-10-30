<?php
/**
 * All assignment related functions
 */
namespace Codexpert\CoSchool\App\Course;

/**
 * if accessed directly, exit.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @package Plugin
 * @subpackage Course
 * @author Codexpert <hi@codexpert.io>
 */
class Taxonomy {

	public function register() {
	
		$category_labels = array(
			'name'                  => _x( 'Categories', 'Taxonomy plural name', 'coschool' ),
			'singular_name'         => _x( 'Category', 'Taxonomy singular name', 'coschool' ),
			'search_items'          => __( 'Search Categories', 'coschool' ),
			'popular_items'         => __( 'Popular Categories', 'coschool' ),
			'all_items'             => __( 'All Categories', 'coschool' ),
			'parent_item'           => __( 'Parent Category', 'coschool' ),
			'parent_item_colon'     => __( 'Parent Category', 'coschool' ),
			'edit_item'             => __( 'Edit Category', 'coschool' ),
			'update_item'           => __( 'Update Category', 'coschool' ),
			'add_new_item'          => __( 'Add New Category', 'coschool' ),
			'new_item_name'         => __( 'New Category Name', 'coschool' ),
			'add_or_remove_items'   => __( 'Add or remove Categories', 'coschool' ),
			'choose_from_most_used' => __( 'Choose from most used Categories', 'coschool' ),
			'menu_name'             => __( 'Category', 'coschool' ),
		);
	
		$category_args = array(
			'labels'            => $category_labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
			'capabilities'      => array(),
		);
	
		register_taxonomy( 'course-category', array( 'course' ), $category_args );
	
		$difficulty_labels = array(
			'name'                  => _x( 'Difficulty Levels', 'Taxonomy plural name', 'coschool' ),
			'singular_name'         => _x( 'Difficulty Level', 'Taxonomy singular name', 'coschool' ),
			'search_items'          => __( 'Search Difficulty Levels', 'coschool' ),
			'popular_items'         => __( 'Popular Difficulty Levels', 'coschool' ),
			'all_items'             => __( 'All Difficulty Levels', 'coschool' ),
			'parent_item'           => __( 'Parent Difficulty Level', 'coschool' ),
			'parent_item_colon'     => __( 'Parent Difficulty Level', 'coschool' ),
			'edit_item'             => __( 'Edit Difficulty Level', 'coschool' ),
			'update_item'           => __( 'Update Difficulty Level', 'coschool' ),
			'add_new_item'          => __( 'Add New Difficulty Level', 'coschool' ),
			'new_item_name'         => __( 'New Difficulty Level Name', 'coschool' ),
			'add_or_remove_items'   => __( 'Add or remove Difficulty Levels', 'coschool' ),
			'choose_from_most_used' => __( 'Choose from most used Difficulty Levels', 'coschool' ),
			'menu_name'             => __( 'Difficulty Level', 'coschool' ),
		);
	
		$difficulty_args = array(
			'labels'            => $difficulty_labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
			'capabilities'      => array(),
		);
	
		register_taxonomy( 'course-difficulty', array( 'course' ), $difficulty_args );
	
		$language_labels = array(
			'name'                  => _x( 'Languages', 'Taxonomy plural name', 'coschool' ),
			'singular_name'         => _x( 'Language', 'Taxonomy singular name', 'coschool' ),
			'search_items'          => __( 'Search Languages', 'coschool' ),
			'popular_items'         => __( 'Popular Languages', 'coschool' ),
			'all_items'             => __( 'All Languages', 'coschool' ),
			'parent_item'           => __( 'Parent Language', 'coschool' ),
			'parent_item_colon'     => __( 'Parent Language', 'coschool' ),
			'edit_item'             => __( 'Edit Language', 'coschool' ),
			'update_item'           => __( 'Update Language', 'coschool' ),
			'add_new_item'          => __( 'Add New Language', 'coschool' ),
			'new_item_name'         => __( 'New Language Name', 'coschool' ),
			'add_or_remove_items'   => __( 'Add or remove Languages', 'coschool' ),
			'choose_from_most_used' => __( 'Choose from most used Languages', 'coschool' ),
			'menu_name'             => __( 'Language', 'coschool' ),
		);
	
		$language_args = array(
			'labels'            => $language_labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => true,
			'hierarchical'      => true,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
			'capabilities'      => array(),
		);
	
		register_taxonomy( 'course-language', array( 'course' ), $language_args );
	
		$keyword_labels = array(
			'name'                  => _x( 'Keywords', 'Taxonomy plural name', 'coschool' ),
			'singular_name'         => _x( 'Keyword', 'Taxonomy singular name', 'coschool' ),
			'search_items'          => __( 'Search Keywords', 'coschool' ),
			'popular_items'         => __( 'Popular Keywords', 'coschool' ),
			'all_items'             => __( 'All Keywords', 'coschool' ),
			'parent_item'           => __( 'Parent Keyword', 'coschool' ),
			'parent_item_colon'     => __( 'Parent Keyword', 'coschool' ),
			'edit_item'             => __( 'Edit Keyword', 'coschool' ),
			'update_item'           => __( 'Update Keyword', 'coschool' ),
			'add_new_item'          => __( 'Add New Keyword', 'coschool' ),
			'new_item_name'         => __( 'New Keyword Name', 'coschool' ),
			'add_or_remove_items'   => __( 'Add or remove Keywords', 'coschool' ),
			'choose_from_most_used' => __( 'Choose from most used Keywords', 'coschool' ),
			'menu_name'             => __( 'Keyword', 'coschool' ),
		);
	
		$keyword_args = array(
			'labels'            => $keyword_labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_admin_column' => false,
			'hierarchical'      => false,
			'show_tagcloud'     => true,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => true,
			'query_var'         => true,
			'capabilities'      => array(),
		);
	
		register_taxonomy( 'course-tag', array( 'course' ), $keyword_args );
	}
}