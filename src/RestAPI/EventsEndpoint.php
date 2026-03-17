<?php
/**
 * Registers and handles the Events REST API endpoint.
 *
 * Endpoint: GET /wp-json/tpots/v1/events
 *
 * Supports query parameters:
 *   ?date=YYYY-MM-DD  — filter events on or after this date
 *   ?limit=N          — maximum number of results (1–100, default 10)
 *
 * Returns only published events with a future or today date,
 * sorted ascending by event date.
 *
 * Authentication: Public — no login required.
 *
 * @package TPots\EventBooking\RestAPI
 */

namespace TPots\EventBooking\RestAPI;

use TPots\EventBooking\MetaBox\EventMetaBox;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Query;

/**
 * Class EventsEndpoint
 *
 * Handles the tpots/v1/events REST route.
 */
class EventsEndpoint {

	/**
	 * The REST API namespace.
	 *
	 * @var string NAMESPACE
	 */
	const NAMESPACE = 'tpots/v1';

	/**
	 * The REST API route.
	 *
	 * @var string ROUTE
	 */
	const ROUTE = '/events';

	/**
	 * Register the REST route with WordPress.
	 *
	 * Hooked to 'rest_api_init' via the Loader.
	 * Using WP_REST_Server::READABLE (GET) and permission_callback
	 * => '__return_true' explicitly declares this as a public endpoint.
	 */
	public function register_routes(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_events' ),
				// Public endpoint — no authentication required.
				'permission_callback' => '__return_true',
				'args'                => $this->get_route_args(),
			)
		);
	}

	/**
	 * Define and return the registered arguments for this route.
	 *
	 * WordPress uses these for automatic sanitization and validation
	 * before our callback is even called.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	private function get_route_args(): array {
		return array(
			'date'  => array(
				'description'       => __( 'Filter events on or after this date (YYYY-MM-DD).', 'event-booking-manager-pro' ),
				'type'              => 'string',
				'required'          => false,
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( $this, 'validate_date_param' ),
			),
			'limit' => array(
				'description'       => __( 'Maximum number of events to return (1–100).', 'event-booking-manager-pro' ),
				'type'              => 'integer',
				'required'          => false,
				'default'           => 10,
				'minimum'           => 1,
				'maximum'           => 100,
				'sanitize_callback' => 'absint',
			),
		);
	}

	/**
	 * Handle the GET /tpots/v1/events request.
	 *
	 * Builds a WP_Query for upcoming published events, applies
	 * optional ?date and ?limit filters, and returns each event
	 * as a formatted object.
	 *
	 * @param WP_REST_Request $request The REST request object.
	 * @return WP_REST_Response
	 */
	public function get_events( WP_REST_Request $request ): WP_REST_Response {
		$limit      = (int) $request->get_param( 'limit' );
		$date_param = $request->get_param( 'date' );

		// Use the provided ?date filter or fall back to today.
		// current_time() returns site-local time, not UTC.
		$from_date = ( ! empty( $date_param ) ) ? $date_param : current_time( 'Y-m-d' );

		$args = array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,

			// Sort by the _event_date meta value, ascending (earliest first).
			'meta_key'       => EventMetaBox::META_DATE,
			'orderby'        => 'meta_value',
			'order'          => 'ASC',

			// Only return events on or after $from_date.
			// type => DATE tells WP to compare as proper DATE, not string.
			'meta_query'     => array(
				array(
					'key'     => EventMetaBox::META_DATE,
					'value'   => $from_date,
					'compare' => '>=',
					'type'    => 'DATE',
				),
			),
		);

		$query  = new WP_Query( $args );
		$events = array();

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post ) {
				$events[] = $this->format_event( $post );
			}
		}

		// Reset global $post to avoid side effects.
		wp_reset_postdata();

		return new WP_REST_Response( $events, 200 );
	}

	/**
	 * Format a single event post into the API response shape.
	 *
	 * @param \WP_Post $post The event post object.
	 * @return array<string, mixed>
	 */
	private function format_event( \WP_Post $post ): array {
		return array(
			'id'              => (int) $post->ID,
			'title'           => get_the_title( $post ),
			'date'            => (string) get_post_meta( $post->ID, EventMetaBox::META_DATE, true ),
			'time'            => (string) get_post_meta( $post->ID, EventMetaBox::META_TIME, true ),
			'location'        => (string) get_post_meta( $post->ID, EventMetaBox::META_LOCATION, true ),
			'available_seats' => (int) get_post_meta( $post->ID, EventMetaBox::META_SEATS, true ),
			'booking_status'  => (string) get_post_meta( $post->ID, EventMetaBox::META_STATUS, true ),
			'permalink'       => get_permalink( $post ),
		);
	}

	/**
	 * Validate the ?date query parameter.
	 *
	 * Returns true if the value is a valid Y-m-d date string,
	 * or a WP_Error if it fails validation.
	 *
	 * @param mixed $value The raw value from the request.
	 * @return bool|\WP_Error
	 */
	public function validate_date_param( mixed $value ): bool|\WP_Error {
		if ( empty( $value ) ) {
			return true; // Optional param — empty is fine.
		}

		$date = sanitize_text_field( $value );

		// Check Y-m-d format.
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			return new \WP_Error(
				'invalid_date_format',
				__( 'The date parameter must be in YYYY-MM-DD format.', 'event-booking-manager-pro' ),
				array( 'status' => 400 )
			);
		}

		// Check it is a real calendar date.
		$parts = explode( '-', $date );
		if ( ! checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] ) ) {
			return new \WP_Error(
				'invalid_date_value',
				__( 'The date parameter is not a valid calendar date.', 'event-booking-manager-pro' ),
				array( 'status' => 400 )
			);
		}

		return true;
	}
}
