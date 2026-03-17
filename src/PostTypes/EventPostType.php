<?php
/**
 * Registers the 'event' Custom Post Type.
 *
 * Handles all CPT arguments including labels, rewrite slug,
 * REST API support, capabilities, and menu configuration.
 *
 * @package TPots\EventBooking\PostTypes
 */

namespace TPots\EventBooking\PostTypes;

/**
 * Class EventPostType
 *
 * Registers and configures the 'event' CPT.
 */
class EventPostType {

	/**
	 * The post type slug.
	 *
	 * @var string POST_TYPE
	 */
	const POST_TYPE = 'event';

	/**
	 * Register the 'event' Custom Post Type with WordPress.
	 *
	 * Hooked to 'init' via the Loader.
	 */
	public function register(): void {
		$labels = array(
			'name'                  => _x( 'Events', 'Post type general name', 'event-booking-manager-pro' ),
			'singular_name'         => _x( 'Event', 'Post type singular name', 'event-booking-manager-pro' ),
			'menu_name'             => _x( 'Events', 'Admin Menu text', 'event-booking-manager-pro' ),
			'name_admin_bar'        => _x( 'Event', 'Add New on Toolbar', 'event-booking-manager-pro' ),
			'add_new'               => __( 'Add New', 'event-booking-manager-pro' ),
			'add_new_item'          => __( 'Add New Event', 'event-booking-manager-pro' ),
			'new_item'              => __( 'New Event', 'event-booking-manager-pro' ),
			'edit_item'             => __( 'Edit Event', 'event-booking-manager-pro' ),
			'view_item'             => __( 'View Event', 'event-booking-manager-pro' ),
			'all_items'             => __( 'All Events', 'event-booking-manager-pro' ),
			'search_items'          => __( 'Search Events', 'event-booking-manager-pro' ),
			'not_found'             => __( 'No events found.', 'event-booking-manager-pro' ),
			'not_found_in_trash'    => __( 'No events found in Trash.', 'event-booking-manager-pro' ),
			'featured_image'        => __( 'Event Banner', 'event-booking-manager-pro' ),
			'set_featured_image'    => __( 'Set event banner', 'event-booking-manager-pro' ),
			'remove_featured_image' => __( 'Remove event banner', 'event-booking-manager-pro' ),
			'use_featured_image'    => __( 'Use as event banner', 'event-booking-manager-pro' ),
			'archives'              => __( 'Event Archives', 'event-booking-manager-pro' ),
			'insert_into_item'      => __( 'Insert into event', 'event-booking-manager-pro' ),
			'uploaded_to_this_item' => __( 'Uploaded to this event', 'event-booking-manager-pro' ),
			'filter_items_list'     => __( 'Filter events list', 'event-booking-manager-pro' ),
			'items_list_navigation' => __( 'Events list navigation', 'event-booking-manager-pro' ),
			'items_list'            => __( 'Events list', 'event-booking-manager-pro' ),
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'Manage public events with booking information.', 'event-booking-manager-pro' ),

			// Visibility.
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_nav_menus'  => true,
			'show_in_admin_bar'  => true,

			// REST API — enables Gutenberg and external REST access.
			'show_in_rest'       => true,
			'rest_base'          => 'events',

			// Querying.
			'query_var'          => true,
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,

			// Dashicons calendar icon for the admin menu.
			'menu_icon'          => 'dashicons-calendar-alt',

			// URL rewrite: /events/event-name/.
			'rewrite'            => array(
				'slug'       => 'events',
				'with_front' => true,
				'feeds'      => false,
				'pages'      => true,
			),

			// Supported features on the edit screen.
			'supports'           => array(
				'title',
				'editor',
				'thumbnail',
			),

			'capability_type'    => 'post',
			'map_meta_cap'       => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}
}
