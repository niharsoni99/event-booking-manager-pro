<?php
/**
 * Enqueues admin-side CSS and JavaScript assets.
 *
 * Loads the meta box stylesheet and optional JavaScript only on
 * the event post edit screen, keeping admin pages clean and fast.
 *
 * @package TPots\EventBooking\Admin
 */

namespace TPots\EventBooking\Admin;

/**
 * Class AdminAssets
 *
 * Handles admin asset enqueueing for the event edit screen.
 */
class AdminAssets {

	/**
	 * The plugin slug, used for script/style handles.
	 *
	 * @var string $plugin_name
	 */
	private string $plugin_name;

	/**
	 * The plugin version, used for cache-busting.
	 *
	 * @var string $version
	 */
	private string $version;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name The plugin slug.
	 * @param string $version     The plugin version.
	 */
	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Enqueue admin stylesheets.
	 *
	 * Only loads on the event post type edit/new screens to
	 * avoid polluting other admin pages.
	 *
	 * Hooked to 'admin_enqueue_scripts' via the Loader.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 */
	public function enqueue_styles( string $hook_suffix ): void {
		if ( ! $this->is_event_edit_screen( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_style(
			$this->plugin_name . '-admin',
			EBMP_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Enqueue admin JavaScript.
	 *
	 * Only loads on the event post type edit/new screens.
	 *
	 * Hooked to 'admin_enqueue_scripts' via the Loader.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 */
	public function enqueue_scripts( string $hook_suffix ): void {
		if ( ! $this->is_event_edit_screen( $hook_suffix ) ) {
			return;
		}

		wp_enqueue_script(
			$this->plugin_name . '-admin',
			EBMP_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			$this->version,
			true // Load in footer.
		);

		// Pass plugin data to JS if needed.
		wp_localize_script(
			$this->plugin_name . '-admin',
			'ebmpAdmin',
			array(
				'pluginUrl' => EBMP_PLUGIN_URL,
				'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Check whether the current admin screen is an event post edit screen.
	 *
	 * Matches both the new post screen (post-new.php) and the
	 * edit post screen (post.php) for the 'event' post type.
	 *
	 * @param string $hook_suffix The current admin page hook suffix.
	 * @return bool True if we are on the event edit screen.
	 */
	private function is_event_edit_screen( string $hook_suffix ): bool {
		$screen = get_current_screen();

		if ( null === $screen ) {
			return false;
		}

		return (
			( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) &&
			'event' === $screen->post_type
		);
	}
}
