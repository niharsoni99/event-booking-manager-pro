<?php
/**
 * Fired during plugin activation and deactivation.
 *
 * Handles flushing rewrite rules so the event CPT permalink
 * structure works immediately after activation without requiring
 * a manual Settings > Permalinks save.
 *
 * @package TPots\EventBooking\Core
 */

namespace TPots\EventBooking\Core;

use TPots\EventBooking\PostTypes\EventPostType;

/**
 * Class Activator
 *
 * Runs on plugin activation and deactivation.
 */
class Activator {

	/**
	 * Plugin activation routine.
	 *
	 * Registers the CPT first so its rewrite rules exist,
	 * then flushes the rewrite rules to make them active immediately.
	 * Without this, visiting /events/ would return a 404 until the
	 * admin manually re-saves the Permalinks settings page.
	 */
	public static function activate(): void {
		// Register the CPT so its rules are available to flush.
		$event_post_type = new EventPostType();
		$event_post_type->register();

		// Flush rewrite rules — only on activation, never on every request.
		flush_rewrite_rules();

		// Store the plugin version for future upgrade routines.
		update_option( 'ebmp_version', EBMP_VERSION );
	}

	/**
	 * Plugin deactivation routine.
	 *
	 * Flushes rewrite rules to remove the CPT slug from WordPress
	 * routing so it doesn't conflict with anything after deactivation.
	 */
	public static function deactivate(): void {
		flush_rewrite_rules();
	}
}
