<?php
/**
 * Loads plugin templates for the event post type.
 *
 * WordPress looks for single-event.php and archive-event.php
 * in the active theme first. If not found there, this class
 * falls back to the templates/ directory inside our plugin.
 *
 * This allows themes to override plugin templates by placing
 * their own versions in the theme root.
 *
 * @package TPots\EventBooking\Frontend
 */

namespace TPots\EventBooking\Frontend;

/**
 * Class TemplateLoader
 *
 * Filters WordPress template resolution for the event post type.
 */
class TemplateLoader {

	/**
	 * Intercept template loading for single event pages.
	 *
	 * Hooked to 'single_template' via the Loader.
	 *
	 * @param string $template The default template path resolved by WP.
	 * @return string The plugin template path, or the original if theme overrides.
	 */
	public function load_single_template( string $template ): string {
		global $post;

		if ( ! $post || 'event' !== $post->post_type ) {
			return $template;
		}

		// Allow theme override: if theme has single-event.php, use that.
		$theme_template = locate_template( array( 'single-event.php' ) );
		if ( $theme_template ) {
			return $theme_template;
		}

		// Fall back to our plugin template.
		$plugin_template = EBMP_PLUGIN_DIR . 'templates/single-event.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		return $template;
	}

	/**
	 * Intercept template loading for the events archive page.
	 *
	 * Hooked to 'archive_template' via the Loader.
	 *
	 * @param string $template The default template path resolved by WP.
	 * @return string The plugin template path, or the original if theme overrides.
	 */
	public function load_archive_template( string $template ): string {
		if ( ! is_post_type_archive( 'event' ) ) {
			return $template;
		}

		// Allow theme override: if theme has archive-event.php, use that.
		$theme_template = locate_template( array( 'archive-event.php' ) );
		if ( $theme_template ) {
			return $theme_template;
		}

		// Fall back to our plugin template.
		$plugin_template = EBMP_PLUGIN_DIR . 'templates/archive-event.php';
		if ( file_exists( $plugin_template ) ) {
			return $plugin_template;
		}

		return $template;
	}

	/**
	 * Enqueue frontend styles for event pages only.
	 *
	 * Hooked to 'wp_enqueue_scripts' via the Loader.
	 * Only loads on single event or event archive pages.
	 */
	public function enqueue_frontend_styles(): void {
		if ( ! is_singular( 'event' ) && ! is_post_type_archive( 'event' ) ) {
			return;
		}

		wp_enqueue_style(
			'event-booking-manager-pro-frontend',
			EBMP_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			EBMP_VERSION,
			'all'
		);
	}
}
