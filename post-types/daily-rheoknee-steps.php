<?php

function daily_leg_steps_init() {
	register_post_type( 'daily-leg-steps', array(
		'labels'            => array(
			'name'                => __( 'Daily leg steps', 'daily-my-leg-step-count' ),
			'singular_name'       => __( 'Daily leg steps', 'daily-my-leg-step-count' ),
			'all_items'           => __( 'All Daily leg steps', 'daily-my-leg-step-count' ),
			'new_item'            => __( 'New daily leg steps', 'daily-my-leg-step-count' ),
			'add_new'             => __( 'Add New', 'daily-my-leg-step-count' ),
			'add_new_item'        => __( 'Add New daily leg steps', 'daily-my-leg-step-count' ),
			'edit_item'           => __( 'Edit daily leg steps', 'daily-my-leg-step-count' ),
			'view_item'           => __( 'View daily leg steps', 'daily-my-leg-step-count' ),
			'search_items'        => __( 'Search daily leg steps', 'daily-my-leg-step-count' ),
			'not_found'           => __( 'No daily leg steps found', 'daily-my-leg-step-count' ),
			'not_found_in_trash'  => __( 'No daily leg steps found in trash', 'daily-my-leg-step-count' ),
			'parent_item_colon'   => __( 'Parent daily leg steps', 'daily-my-leg-step-count' ),
			'menu_name'           => __( 'Daily leg steps', 'daily-my-leg-step-count' ),
		),
		'public'            => false,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'supports'          => array( 'title', 'editor', 'thumbnail' ),
		'has_archive'       => true,
		'rewrite'           => true,
		'query_var'         => true,
		'menu_icon'         => 'dashicons-admin-post',
		'show_in_rest'      => true,
		'rest_base'         => 'daily-leg-steps',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	) );

}
add_action( 'init', 'daily_leg_steps_init' );
