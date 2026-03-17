<?php
/**
 * Template for displaying a single event post.
 *
 * Loaded by TemplateLoader when viewing a single event.
 * Shows all meta: date, time, location, seats, booking status.
 *
 * @package TPots\EventBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="ebmp-single-wrap">
	<?php
	while ( have_posts() ) :
		the_post();

		$event_date      = get_post_meta( get_the_ID(), '_event_date', true );
		$event_time      = get_post_meta( get_the_ID(), '_event_time', true );
		$location        = get_post_meta( get_the_ID(), '_event_location', true );
		$available_seats = get_post_meta( get_the_ID(), '_available_seats', true );
		$booking_status  = get_post_meta( get_the_ID(), '_booking_status', true );

		// Format date: 2026-03-18 → March 18, 2026.
		$formatted_date = '';
		if ( ! empty( $event_date ) ) {
			$date_obj       = \DateTime::createFromFormat( 'Y-m-d', $event_date );
			$formatted_date = $date_obj ? $date_obj->format( 'F j, Y' ) : $event_date;
		}

		// Format time: 13:20 → 1:20 PM.
		$formatted_time = '';
		if ( ! empty( $event_time ) ) {
			$time_obj       = \DateTime::createFromFormat( 'H:i', $event_time );
			$formatted_time = $time_obj ? $time_obj->format( 'g:i A' ) : $event_time;
		}

		$status_labels = array(
			'open'      => __( 'Open', 'event-booking-manager-pro' ),
			'closed'    => __( 'Closed', 'event-booking-manager-pro' ),
			'cancelled' => __( 'Cancelled', 'event-booking-manager-pro' ),
		);
		$status_label = isset( $status_labels[ $booking_status ] )
			? $status_labels[ $booking_status ]
			: ucfirst( esc_html( (string) $booking_status ) );
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class( 'ebmp-single-event' ); ?>>

			<header class="ebmp-event-header">
				<div class="ebmp-header-top">
					<h1 class="ebmp-event-title"><?php the_title(); ?></h1>
					<?php if ( ! empty( $booking_status ) ) : ?>
						<span class="ebmp-status-badge ebmp-status-<?php echo esc_attr( $booking_status ); ?>">
							<?php echo esc_html( $status_label ); ?>
						</span>
					<?php endif; ?>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="ebmp-event-thumbnail">
					<?php the_post_thumbnail( 'large' ); ?>
				</div>
			<?php endif; ?>

			<div class="ebmp-event-meta-bar">

				<?php if ( ! empty( $formatted_date ) ) : ?>
					<div class="ebmp-meta-item">
						<span class="ebmp-meta-icon">&#128197;</span>
						<div class="ebmp-meta-content">
							<span class="ebmp-meta-label"><?php esc_html_e( 'Date', 'event-booking-manager-pro' ); ?></span>
							<span class="ebmp-meta-value"><?php echo esc_html( $formatted_date ); ?></span>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $formatted_time ) ) : ?>
					<div class="ebmp-meta-item">
						<span class="ebmp-meta-icon">&#128336;</span>
						<div class="ebmp-meta-content">
							<span class="ebmp-meta-label"><?php esc_html_e( 'Time', 'event-booking-manager-pro' ); ?></span>
							<span class="ebmp-meta-value"><?php echo esc_html( $formatted_time ); ?></span>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $location ) ) : ?>
					<div class="ebmp-meta-item">
						<span class="ebmp-meta-icon">&#128205;</span>
						<div class="ebmp-meta-content">
							<span class="ebmp-meta-label"><?php esc_html_e( 'Location', 'event-booking-manager-pro' ); ?></span>
							<span class="ebmp-meta-value"><?php echo nl2br( esc_html( $location ) ); ?></span>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( '' !== $available_seats && false !== $available_seats ) : ?>
					<div class="ebmp-meta-item">
						<span class="ebmp-meta-icon">&#127903;</span>
						<div class="ebmp-meta-content">
							<span class="ebmp-meta-label"><?php esc_html_e( 'Available Seats', 'event-booking-manager-pro' ); ?></span>
							<span class="ebmp-meta-value"><?php echo esc_html( absint( $available_seats ) ); ?></span>
						</div>
					</div>
				<?php endif; ?>

			</div><!-- .ebmp-event-meta-bar -->

			<?php if ( ! empty( get_the_content() ) ) : ?>
				<div class="ebmp-event-content">
					<h2 class="ebmp-section-title"><?php esc_html_e( 'About This Event', 'event-booking-manager-pro' ); ?></h2>
					<div class="ebmp-content-body">
						<?php the_content(); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="ebmp-back-link">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>">
					&larr; <?php esc_html_e( 'Back to All Events', 'event-booking-manager-pro' ); ?>
				</a>
			</div>

		</article>

	<?php endwhile; ?>
</div><!-- .ebmp-single-wrap -->

<?php get_footer(); ?>
