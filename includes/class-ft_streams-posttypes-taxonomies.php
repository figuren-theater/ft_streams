<?php

/**
 * Define all custom post_types and custom taxonomies
 *
 * ....
 *
 * @link       http://carsten-bach.de
 * @since      2020.03.22
 *
 * @package    Ft_streams
 * @subpackage Ft_streams/includes
 */

/**
 * ....
 *
 * ....
 *
 * @since      2020.03.22
 * @package    Ft_streams
 * @subpackage Ft_streams/includes
 * @author     Carsten Bach <mail@carsten-bach.de>
 */
class Ft_streams_Posttypes_and_Taxonomies {

	/**
	 * Register "Streams" custom post type
	 *
	 * @since 	2020.03.22
	 * @access 	public
	 * @uses 	register_post_type()
	 */
	public static function ft_stream_post_type() {

		$labels = array(
			'name'                  => _x( 'Streams', 'Post Type General Name', 'ft_streams' ),
			'singular_name'         => _x( 'Stream', 'Post Type Singular Name', 'ft_streams' ),
			'menu_name'             => __( 'Streams', 'ft_streams' ),
			'name_admin_bar'        => __( 'Stream', 'ft_streams' ),
			'archives'              => __( 'Stream Archives', 'ft_streams' ),
			'attributes'            => __( 'Stream Attributes', 'ft_streams' ),
			'parent_item_colon'     => __( 'Parent Stream:', 'ft_streams' ),
			'all_items'             => __( 'All Streams', 'ft_streams' ),
			'add_new_item'          => __( 'Add New Stream', 'ft_streams' ),
			'add_new'               => __( 'Add New', 'ft_streams' ),
			'new_item'              => __( 'New Stream', 'ft_streams' ),
			'edit_item'             => __( 'Edit Stream', 'ft_streams' ),
			'update_item'           => __( 'Update Stream', 'ft_streams' ),
			'view_item'             => __( 'View Stream', 'ft_streams' ),
			'view_items'            => __( 'View Streams', 'ft_streams' ),
			'search_items'          => __( 'Search Stream', 'ft_streams' ),
			'not_found'             => __( 'Not found', 'ft_streams' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'ft_streams' ),
			'featured_image'        => __( 'Featured Image', 'ft_streams' ),
			'set_featured_image'    => __( 'Set featured image', 'ft_streams' ),
			'remove_featured_image' => __( 'Remove featured image', 'ft_streams' ),
			'use_featured_image'    => __( 'Use as featured image', 'ft_streams' ),
			'insert_into_item'      => __( 'Insert into stream', 'ft_streams' ),
			'uploaded_to_this_item' => __( 'Uploaded to this stream', 'ft_streams' ),
			'items_list'            => __( 'Streams list', 'ft_streams' ),
			'items_list_navigation' => __( 'Streams list navigation', 'ft_streams' ),
			'filter_items_list'     => __( 'Filter streams list', 'ft_streams' ),
		);
		$rewrite = array(
			'slug'                  => 'streams',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Stream', 'ft_streams' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'comments', 'trackbacks', 'custom-fields', 'post-formats','author' ),
			'taxonomies'            => array( 'stream_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-format-video',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'rewrite'               => $rewrite,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);
		register_post_type( 'ft_stream', $args );

	}




	/**
	 * Feature update messages.
	 *
	 * See /wp-admin/edit-form-advanced.php
	 *
	 * @param array $messages Existing post update messages.
	 *
	 * @return array Amended post update messages with new CPT update messages.
	 */

	public function ft_stream_updated_messages( $messages ) {

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );
		
		$messages['ft_stream'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Stream updated.', 'ft_SALES' ),
			2  => __( 'Custom field updated.', 'ft_SALES' ),
			3  => __( 'Custom field deleted.', 'ft_SALES'),
			4  => __( 'Stream updated.', 'ft_SALES' ),
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Stream restored to revision from %s', 'ft_SALES' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Stream published.', 'ft_SALES' ),
			7  => __( 'Stream saved.', 'ft_SALES' ),
			8  => __( 'Stream submitted.', 'ft_SALES' ),
			9  => sprintf(
				__( 'Stream scheduled for: <strong>%1$s</strong>.', 'ft_SALES' ),
				// translators: Publish box date format, see http://php.net/date
				date_i18n( __( 'M j, Y @ G:i', 'ft_SALES' ), strtotime( $post->post_date ) )
			),
			10 => __( 'Stream draft updated.', 'ft_SALES' )
		);

			//you can also access items this way
			// $messages['post'][1] = "I just totally changed the Updated messages for standards posts";

			//return the new messaging 
		return $messages;
	}

	// Register Custom Taxonomy
	public function ft_stream_tag_taxonomy() {

		$labels = array(
			'name'                       => _x( 'Stream-Tags', 'Taxonomy General Name', 'ft_SALES' ),
			'singular_name'              => _x( 'Stream-Tag', 'Taxonomy Singular Name', 'ft_SALES' ),
			'menu_name'                  => __( 'Stream-Tag', 'ft_SALES' ),
			'all_items'                  => __( 'All Stream-Tags', 'ft_SALES' ),
			'parent_item'                => __( 'Parent Stream-Tag', 'ft_SALES' ),
			'parent_item_colon'          => __( 'Parent Stream-Tag:', 'ft_SALES' ),
			'new_item_name'              => __( 'New Stream-Tag Name', 'ft_SALES' ),
			'add_new_item'               => __( 'Add New Stream-Tag', 'ft_SALES' ),
			'edit_item'                  => __( 'Edit Stream-Tag', 'ft_SALES' ),
			'update_item'                => __( 'Update Stream-Tag', 'ft_SALES' ),
			'view_item'                  => __( 'View Stream-Tag', 'ft_SALES' ),
			'separate_items_with_commas' => __( 'Separate stream-tags with commas', 'ft_SALES' ),
			'add_or_remove_items'        => __( 'Add or remove stream-tags', 'ft_SALES' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'ft_SALES' ),
			'popular_items'              => __( 'Popular Stream-Tags', 'ft_SALES' ),
			'search_items'               => __( 'Search Stream-Tags', 'ft_SALES' ),
			'not_found'                  => __( 'Not Found', 'ft_SALES' ),
			'no_terms'                   => __( 'No stream-tags', 'ft_SALES' ),
			'items_list'                 => __( 'Stream-Tags list', 'ft_SALES' ),
			'items_list_navigation'      => __( 'Stream-Tags list navigation', 'ft_SALES' ),
		);
		$rewrite = array(
			'slug'                       => 'stream-tag',
			'with_front'                 => true,
			'hierarchical'               => false,
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'rewrite'                    => $rewrite,
			'show_in_rest'               => true,
		);
		register_taxonomy( 'ft_stream_tag', array( 'ft_stream' ), $args );

	}



} // Ft_streams_Posttypes_and_Taxonomies