<?php
/**
 * Plugin Name:       Event Booking Manager Pro
 * Plugin URI:        https://github.com/
 * Description:       A production-grade event booking manager with CPT, meta fields, and REST API.
 * Version:           1.0.0
 * Author:            TPots Developer
 * Author URI:        https://example.com
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       event-booking-manager-pro
 * Domain Path:       /languages
 * Requires at least: 6.0
 * Requires PHP:      8.1
 *
 * @package TPots\EventBooking
 */

// Prevent direct file access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'EBMP_VERSION', '1.0.0' );
define( 'EBMP_PLUGIN_FILE', __FILE__ );
define( 'EBMP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EBMP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load Composer PSR-4 autoloader.
if ( file_exists( EBMP_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once EBMP_PLUGIN_DIR . 'vendor/autoload.php';
} else {
	// Show an admin notice if Composer autoloader is missing.
	add_action(
		'admin_notices',
		function () {
			echo '<div class="notice notice-error"><p>';
			echo '<strong>Event Booking Manager Pro:</strong> Composer autoloader not found. Please run <code>composer install</code> in the plugin directory.';
			echo '</p></div>';
		}
	);
	return;
}

// Register activation hook — flush rewrite rules so CPT slug works immediately.
register_activation_hook( __FILE__, array( 'TPots\EventBooking\Core\Activator', 'activate' ) );

// Register deactivation hook — flush rewrite rules to clean up.
register_deactivation_hook( __FILE__, array( 'TPots\EventBooking\Core\Activator', 'deactivate' ) );

/**
 * Begin plugin execution.
 * The core class wires all components through the Loader.
 */
function ebmp_run_plugin() {
	$plugin = new TPots\EventBooking\Core\Plugin();
	$plugin->run();
}

ebmp_run_plugin();
