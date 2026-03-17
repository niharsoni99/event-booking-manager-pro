<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * Removes all plugin-related options from the database.
 * Post meta (_event_date, _event_time, etc.) is intentionally preserved
 * so event data is not lost on accidental uninstall.
 *
 * @package TPots\EventBooking
 */

// Only run if WordPress core triggers this uninstall — never direct access.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove plugin version option if stored.
delete_option( 'ebmp_version' );
