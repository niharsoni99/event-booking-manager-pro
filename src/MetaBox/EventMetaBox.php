<?php
/**
 * Registers, renders, and saves the Event Details meta box.
 *
 * Contains all 5 event meta fields:
 *   - Event Date     (_event_date)
 *   - Event Time     (_event_time)
 *   - Location       (_event_location)
 *   - Available Seats(_available_seats)
 *   - Booking Status (_booking_status)
 *
 * Every field is sanitized on save. Nonce verification, autosave
 * checks, revision checks, and capability checks are all enforced.
 *
 * @package TPots\EventBooking\MetaBox
 */

namespace TPots\EventBooking\MetaBox;

use WP_Post;

/**
 * Class EventMetaBox
 *
 * Manages the Event Details meta box on the event edit screen.
 */
class EventMetaBox {

	/**
	 * Meta key for event date.
	 *
	 * @var string META_DATE
	 */
	const META_DATE = '_event_date';

	/**
	 * Meta key for event time.
	 *
	 * @var string META_TIME
	 */
	const META_TIME = '_event_time';

	/**
	 * Meta key for event location.
	 *
	 * @var string META_LOCATION
	 */
	const META_LOCATION = '_event_location';

	/**
	 * Meta key for available seats.
	 *
	 * @var string META_SEATS
	 */
	const META_SEATS = '_available_seats';

	/**
	 * Meta key for booking status.
	 *
	 * @var string META_STATUS
	 */
	const META_STATUS = '_booking_status';

	/**
	 * Nonce action string for meta box verification.
	 *
	 * @var string NONCE_ACTION
	 */
	const NONCE_ACTION = 'ebmp_save_event_meta';

	/**
	 * Nonce field name in the POST form.
	 *
	 * @var string NONCE_FIELD
	 */
	const NONCE_FIELD = '_ebmp_event_nonce';

	/**
	 * Valid booking status options.
	 *
	 * @var array<string> VALID_STATUSES
	 */
	const VALID_STATUSES = array( 'open', 'closed', 'cancelled' );

	/**
	 * Register the meta box on the event edit screen.
	 *
	 * Hooked to 'add_meta_boxes' via the Loader.
	 */
	public function add_meta_box(): void {
		add_meta_box(
			'ebmp_event_details',              // Unique ID.
			__( 'Event Details', 'event-booking-manager-pro' ), // Title.
			array( $this, 'render_meta_box' ), // Render callback.
			'event',                           // Post type.
			'normal',                          // Context: normal / side / advanced.
			'high'                             // Priority: high / core / default / low.
		);
	}

	/**
	 * Render the meta box HTML on the event edit screen.
	 *
	 * Outputs a nonce field plus all 5 event meta fields.
	 * All existing values are retrieved with get_post_meta() and
	 * escaped before output.
	 *
	 * @param WP_Post $post The current post object.
	 */
	public function render_meta_box( WP_Post $post ): void {
		// Output nonce — verified in save_meta() before any data is saved.
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );

		// Retrieve existing meta values (empty string fallback for new events).
		$event_date    = get_post_meta( $post->ID, self::META_DATE, true );
		$event_time    = get_post_meta( $post->ID, self::META_TIME, true );
		$location      = get_post_meta( $post->ID, self::META_LOCATION, true );
		$seats         = get_post_meta( $post->ID, self::META_SEATS, true );
		$booking_status = get_post_meta( $post->ID, self::META_STATUS, true );

		// Default seats to 0 for new events.
		if ( '' === $seats ) {
			$seats = 0;
		}

		// Default status to 'open' for new events.
		if ( '' === $booking_status ) {
			$booking_status = 'open';
		}
		?>
		<div class="ebmp-meta-box-wrap">

			<div class="ebmp-field-row">
				<div class="ebmp-field-group ebmp-half">
					<label for="ebmp_event_date">
						<?php esc_html_e( 'Event Date', 'event-booking-manager-pro' ); ?>
						<span class="ebmp-required">*</span>
					</label>
					<input
						type="date"
						id="ebmp_event_date"
						name="ebmp_event_date"
						value="<?php echo esc_attr( $event_date ); ?>"
						placeholder="YYYY-MM-DD"
						class="ebmp-input"
					/>
					<p class="ebmp-help-text"><?php esc_html_e( 'Format: YYYY-MM-DD', 'event-booking-manager-pro' ); ?></p>
				</div>

				<div class="ebmp-field-group ebmp-half">
					<label for="ebmp_event_time">
						<?php esc_html_e( 'Event Time', 'event-booking-manager-pro' ); ?>
						<span class="ebmp-required">*</span>
					</label>
					<input
						type="time"
						id="ebmp_event_time"
						name="ebmp_event_time"
						value="<?php echo esc_attr( $event_time ); ?>"
						placeholder="HH:MM"
						class="ebmp-input"
					/>
					<p class="ebmp-help-text"><?php esc_html_e( 'Format: HH:MM (24-hour)', 'event-booking-manager-pro' ); ?></p>
				</div>
			</div>

			<div class="ebmp-field-group">
				<label for="ebmp_event_location">
					<?php esc_html_e( 'Location', 'event-booking-manager-pro' ); ?>
				</label>
				<textarea
					id="ebmp_event_location"
					name="ebmp_event_location"
					rows="3"
					class="ebmp-textarea"
					placeholder="<?php esc_attr_e( 'Enter the event venue or address...', 'event-booking-manager-pro' ); ?>"
				><?php echo esc_textarea( $location ); ?></textarea>
			</div>

			<div class="ebmp-field-row">
				<div class="ebmp-field-group ebmp-half">
					<label for="ebmp_available_seats">
						<?php esc_html_e( 'Available Seats', 'event-booking-manager-pro' ); ?>
					</label>
					<input
						type="number"
						id="ebmp_available_seats"
						name="ebmp_available_seats"
						value="<?php echo esc_attr( $seats ); ?>"
						min="0"
						step="1"
						class="ebmp-input"
					/>
					<p class="ebmp-help-text"><?php esc_html_e( 'Minimum: 0', 'event-booking-manager-pro' ); ?></p>
				</div>

				<div class="ebmp-field-group ebmp-half">
					<label for="ebmp_booking_status">
						<?php esc_html_e( 'Booking Status', 'event-booking-manager-pro' ); ?>
					</label>
					<select
						id="ebmp_booking_status"
						name="ebmp_booking_status"
						class="ebmp-select"
					>
						<option value="open" <?php selected( $booking_status, 'open' ); ?>>
							<?php esc_html_e( 'Open', 'event-booking-manager-pro' ); ?>
						</option>
						<option value="closed" <?php selected( $booking_status, 'closed' ); ?>>
							<?php esc_html_e( 'Closed', 'event-booking-manager-pro' ); ?>
						</option>
						<option value="cancelled" <?php selected( $booking_status, 'cancelled' ); ?>>
							<?php esc_html_e( 'Cancelled', 'event-booking-manager-pro' ); ?>
						</option>
					</select>
				</div>
			</div>

		</div><!-- .ebmp-meta-box-wrap -->
		<?php
	}

	/**
	 * Save event meta fields when the post is saved.
	 *
	 * Security checks performed in order before saving anything:
	 *   1. Nonce verification
	 *   2. Autosave bail-out
	 *   3. Revision bail-out
	 *   4. User capability check
	 *
	 * Each field is individually sanitized before update_post_meta().
	 *
	 * Hooked to 'save_post_event' via the Loader (fires only for CPT 'event').
	 *
	 * @param int     $post_id The ID of the post being saved.
	 * @param WP_Post $post    The post object.
	 */
	public function save_meta( int $post_id, WP_Post $post ): void {

		// ── 1. Nonce verification ─────────────────────────────────────────
		// The nonce field must exist and be valid. If it fails, stop immediately.
		if (
			! isset( $_POST[ self::NONCE_FIELD ] ) ||
			! wp_verify_nonce(
				sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ),
				self::NONCE_ACTION
			)
		) {
			return;
		}

		// ── 2. Autosave bail-out ──────────────────────────────────────────
		// WordPress fires save_post during autosave. We skip it to avoid
		// saving potentially incomplete data from the autosave request.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// ── 3. Revision bail-out ──────────────────────────────────────────
		// Never save meta to a revision; only save to the real post.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// ── 4. Capability check ───────────────────────────────────────────
		// Ensure the current user has permission to edit this specific post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// ── 5. Save each field with proper sanitization ───────────────────

		// Event Date — expected Y-m-d. Sanitize then validate format.
		if ( isset( $_POST['ebmp_event_date'] ) ) {
			$raw_date = sanitize_text_field( wp_unslash( $_POST['ebmp_event_date'] ) );
			// Validate Y-m-d format before saving.
			if ( $this->is_valid_date( $raw_date ) ) {
				update_post_meta( $post_id, self::META_DATE, $raw_date );
			} elseif ( '' === $raw_date ) {
				// Allow clearing the field.
				delete_post_meta( $post_id, self::META_DATE );
			}
		}

		// Event Time — expected HH:MM.
		if ( isset( $_POST['ebmp_event_time'] ) ) {
			$raw_time = sanitize_text_field( wp_unslash( $_POST['ebmp_event_time'] ) );
			if ( $this->is_valid_time( $raw_time ) ) {
				update_post_meta( $post_id, self::META_TIME, $raw_time );
			} elseif ( '' === $raw_time ) {
				delete_post_meta( $post_id, self::META_TIME );
			}
		}

		// Location — textarea, preserve newlines with sanitize_textarea_field.
		if ( isset( $_POST['ebmp_event_location'] ) ) {
			$location = sanitize_textarea_field( wp_unslash( $_POST['ebmp_event_location'] ) );
			update_post_meta( $post_id, self::META_LOCATION, $location );
		}

		// Available Seats — absint() guarantees a non-negative integer.
		if ( isset( $_POST['ebmp_available_seats'] ) ) {
			$seats = absint( $_POST['ebmp_available_seats'] );
			update_post_meta( $post_id, self::META_SEATS, $seats );
		}

		// Booking Status — whitelist check; only save if value is allowed.
		if ( isset( $_POST['ebmp_booking_status'] ) ) {
			$raw_status = sanitize_text_field( wp_unslash( $_POST['ebmp_booking_status'] ) );
			if ( in_array( $raw_status, self::VALID_STATUSES, true ) ) {
				update_post_meta( $post_id, self::META_STATUS, $raw_status );
			}
		}
	}

	/**
	 * Validate that a string matches the Y-m-d date format and is a real date.
	 *
	 * @param string $date The date string to validate.
	 * @return bool True if valid, false otherwise.
	 */
	private function is_valid_date( string $date ): bool {
		if ( empty( $date ) ) {
			return false;
		}

		// Check format with regex first.
		if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
			return false;
		}

		// Use checkdate() to confirm the date is a real calendar date.
		$parts = explode( '-', $date );

		return checkdate( (int) $parts[1], (int) $parts[2], (int) $parts[0] );
	}

	/**
	 * Validate that a string matches HH:MM time format.
	 *
	 * @param string $time The time string to validate.
	 * @return bool True if valid, false otherwise.
	 */
	private function is_valid_time( string $time ): bool {
		if ( empty( $time ) ) {
			return false;
		}

		return (bool) preg_match( '/^([01]\d|2[0-3]):([0-5]\d)$/', $time );
	}
}
