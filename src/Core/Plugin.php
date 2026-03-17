<?php
/**
 * The core plugin class.
 *
 * Central bootstrap — instantiates all service objects,
 * wires them together, and registers all hooks through
 * the Loader. The main plugin file only calls Plugin::run().
 *
 * @package TPots\EventBooking\Core
 */

namespace TPots\EventBooking\Core;

use TPots\EventBooking\Admin\AdminAssets;
use TPots\EventBooking\Admin\AdminColumns;
use TPots\EventBooking\Frontend\TemplateLoader;
use TPots\EventBooking\MetaBox\EventMetaBox;
use TPots\EventBooking\PostTypes\EventPostType;
use TPots\EventBooking\RestAPI\EventsEndpoint;

/**
 * Class Plugin
 *
 * Orchestrates all plugin components via the Loader.
 */
class Plugin {

	/**
	 * The Loader — single registry for all WP hooks.
	 *
	 * @var Loader $loader
	 */
	private Loader $loader;

	/**
	 * The plugin slug.
	 *
	 * @var string $plugin_name
	 */
	private string $plugin_name;

	/**
	 * The current plugin version.
	 *
	 * @var string $version
	 */
	private string $version;

	/**
	 * Constructor — initialises the Loader and defines all hooks.
	 */
	public function __construct() {
		$this->plugin_name = 'event-booking-manager-pro';
		$this->version     = EBMP_VERSION;
		$this->loader      = new Loader();

		$this->define_post_type_hooks();
		$this->define_meta_box_hooks();
		$this->define_rest_api_hooks();
		$this->define_admin_hooks();
		$this->define_frontend_hooks();
	}

	/**
	 * Register CPT hooks.
	 */
	private function define_post_type_hooks(): void {
		$event_post_type = new EventPostType();
		$this->loader->add_action( 'init', $event_post_type, 'register' );
	}

	/**
	 * Register Meta Box hooks.
	 */
	private function define_meta_box_hooks(): void {
		$event_meta_box = new EventMetaBox();
		$this->loader->add_action( 'add_meta_boxes', $event_meta_box, 'add_meta_box' );
		$this->loader->add_action( 'save_post_event', $event_meta_box, 'save_meta', 10, 2 );
	}

	/**
	 * Register REST API hooks.
	 */
	private function define_rest_api_hooks(): void {
		$events_endpoint = new EventsEndpoint();
		$this->loader->add_action( 'rest_api_init', $events_endpoint, 'register_routes' );
	}

	/**
	 * Register admin hooks — assets and list table columns.
	 */
	private function define_admin_hooks(): void {
		$admin_assets = new AdminAssets( $this->plugin_name, $this->version );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin_assets, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $admin_assets, 'enqueue_scripts' );

		$admin_columns = new AdminColumns();
		$this->loader->add_filter( 'manage_event_posts_columns', $admin_columns, 'add_columns' );
		$this->loader->add_action( 'manage_event_posts_custom_column', $admin_columns, 'render_columns', 10, 2 );
		$this->loader->add_filter( 'manage_edit-event_sortable_columns', $admin_columns, 'sortable_columns' );
		$this->loader->add_action( 'pre_get_posts', $admin_columns, 'handle_sortable_orderby' );
	}

	/**
	 * Register frontend template and style hooks.
	 */
	private function define_frontend_hooks(): void {
		$template_loader = new TemplateLoader();
		$this->loader->add_filter( 'single_template', $template_loader, 'load_single_template' );
		$this->loader->add_filter( 'archive_template', $template_loader, 'load_archive_template' );
		$this->loader->add_action( 'wp_enqueue_scripts', $template_loader, 'enqueue_frontend_styles' );
	}

	/**
	 * Run the plugin — fire all registered hooks with WordPress.
	 */
	public function run(): void {
		$this->loader->run();
	}

	/**
	 * Get the plugin slug.
	 *
	 * @return string
	 */
	public function get_plugin_name(): string {
		return $this->plugin_name;
	}

	/**
	 * Get the plugin version.
	 *
	 * @return string
	 */
	public function get_version(): string {
		return $this->version;
	}

	/**
	 * Get the Loader instance.
	 *
	 * @return Loader
	 */
	public function get_loader(): Loader {
		return $this->loader;
	}
}
