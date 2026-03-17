<?php
/**
 * Adds custom columns to the Events admin list table.
 *
 * Adds Date, Time, Location, and Booking Status columns
 * to the WP Admin > Events list screen, and makes the
 * Date column sortable.
 *
 * @package TPots\EventBooking\Admin
 */

namespace TPots\EventBooking\Admin;

use TPots\EventBooking\MetaBox\EventMetaBox;

/**
 * Class AdminColumns
 *
 * Manages custom columns on the event post list screen.
 */
class AdminColumns {

	/**
	 * Register custom columns for the event post type.
	 *
	 * Hooked to 'manage_event_posts_columns' via the Loader.
	 *
	 * @param array<string, string> $columns Default columns.
	 * @return array<string, string> Modified columns.
	 */
	public function add_columns( array $columns ): array {
		// Remove the default date column — we'll add our own event date.
		unset( $columns['date'] );

		$columns['event_date']   = __( 'Event Date', 'event-booking-manager-pro' );
		$columns['event_time']   = __( 'Time', 'event-booking-manager-pro' );
		$columns['event_location'] = __( 'Location', 'event-booking-manager-pro' );
		$columns['event_seats']  = __( 'Seats', 'event-booking-manager-pro' );
		$columns['event_status'] = __( 'Booking Status', 'event-booking-manager-pro' );

		return $columns;
	}

	/**
	 * Render the content for each custom column row.
	 *
	 * Hooked to 'manage_event_posts_custom_column' via the Loader.
	 *
	 * @param string $column  The column slug.
	 * @param int    $post_id The current post ID.
	 */
	public function render_columns( string $column, int $post_id ): void {
		switch ( $column ) {

			case 'event_date':
				$date = get_post_meta( $post_id, EventMetaBox::META_DATE, true );
				if ( ! empty( $date ) ) {
					$date_obj = \DateTime::createFromFormat( 'Y-m-d', $date );
					echo esc_html( $date_obj ? $date_obj->format( 'M j, Y' ) : $date );
				} else {
					echo '<span style="color:#aaa;">—</span>';
				}
				break;

			case 'event_time':
				$time = get_post_meta( $post_id, EventMetaBox::META_TIME, true );
				if ( ! empty( $time ) ) {
					$time_obj = \DateTime::createFromFormat( 'H:i', $time );
					echo esc_html( $time_obj ? $time_obj->format( 'g:i A' ) : $time );
				} else {
					echo '<span style="color:#aaa;">—</span>';
				}
				break;

			case 'event_location':
				$location = get_post_meta( $post_id, EventMetaBox::META_LOCATION, true );
				if ( ! empty( $location ) ) {
					echo esc_html( wp_trim_words( $location, 6, '...' ) );
				} else {
					echo '<span style="color:#aaa;">—</span>';
				}
				break;

			case 'event_seats':
				$seats = get_post_meta( $post_id, EventMetaBox::META_SEATS, true );
				if ( '' !== $seats ) {
					echo esc_html( absint( $seats ) );
				} else {
					echo '<span style="color:#aaa;">—</span>';
				}
				break;

			case 'event_status':
				$status = get_post_meta( $post_id, EventMetaBox::META_STATUS, true );

				$colors = array(
					'open'      => '#00a32a',
					'closed'    => '#d63638',
					'cancelled' => '#dba617',
				);
				$labels = array(
					'open'      => __( 'Open', 'event-booking-manager-pro' ),
					'closed'    => __( 'Closed', 'event-booking-manager-pro' ),
					'cancelled' => __( 'Cancelled', 'event-booking-manager-pro' ),
				);

				if ( ! empty( $status ) && isset( $labels[ $status ] ) ) {
					$color = $colors[ $status ];
					$label = $labels[ $status ];
					printf(
						'<span style="display:inline-block;padding:2px 10px;border-radius:12px;background:%s;color:#fff;font-size:11px;font-weight:600;">%s</span>',
						esc_attr( $color ),
						esc_html( $label )
					);
				} else {
					echo '<span style="color:#aaa;">—</span>';
				}
				break;
		}
	}

	/**
	 * Register sortable columns.
	 *
	 * Hooked to 'manage_edit-event_sortable_columns' via the Loader.
	 *
	 * @param array<string, string> $columns Existing sortable columns.
	 * @return array<string, string> Modified sortable columns.
	 */
	public function sortable_columns( array $columns ): array {
		$columns['event_date']   = 'event_date';
		$columns['event_status'] = 'event_status';

		return $columns;
	}

	/**
	 * Handle orderby for sortable custom columns.
	 *
	 * Hooked to 'pre_get_posts' via the Loader.
	 *
	 * @param \WP_Query $query The WP_Query instance.
	 */
	public function handle_sortable_orderby( \WP_Query $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		if ( 'event_date' === $orderby ) {
			$query->set( 'meta_key', EventMetaBox::META_DATE );
			$query->set( 'orderby', 'meta_value' );
		}

		if ( 'event_status' === $orderby ) {
			$query->set( 'meta_key', EventMetaBox::META_STATUS );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}
