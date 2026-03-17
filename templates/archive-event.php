<?php
/**
 * Template for displaying the events archive.
 *
 * Loaded by TemplateLoader when visiting /events/.
 * Shows all published events as cards with meta info.
 *
 * @package TPots\EventBooking
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<div class="ebmp-archive-wrap">

	<header class="ebmp-archive-header">
		<h1 class="ebmp-archive-title"><?php esc_html_e( 'Upcoming Events', 'event-booking-manager-pro' ); ?></h1>
		<p class="ebmp-archive-subtitle"><?php esc_html_e( 'Browse and book events below.', 'event-booking-manager-pro' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>

		<div class="ebmp-events-grid">
			<?php
			while ( have_posts() ) :
				the_post();

				$event_date      = get_post_meta( get_the_ID(), '_event_date', true );
				$event_time      = get_post_meta( get_the_ID(), '_event_time', true );
				$location        = get_post_meta( get_the_ID(), '_event_location', true );
				$available_seats = get_post_meta( get_the_ID(), '_available_seats', true );
				$booking_status  = get_post_meta( get_the_ID(), '_booking_status', true );

				$formatted_date = '';
				if ( ! empty( $event_date ) ) {
					$date_obj       = \DateTime::createFromFormat( 'Y-m-d', $event_date );
					$formatted_date = $date_obj ? $date_obj->format( 'M j, Y' ) : $event_date;
				}

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

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'ebmp-event-card' ); ?>>

					<?php if ( has_post_thumbnail() ) : ?>
						<a href="<?php the_permalink(); ?>" class="ebmp-card-image-link">
							<div class="ebmp-card-image">
								<?php the_post_thumbnail( 'medium_large' ); ?>
								<?php if ( ! empty( $booking_status ) ) : ?>
									<span class="ebmp-card-badge ebmp-status-<?php echo esc_attr( $booking_status ); ?>">
										<?php echo esc_html( $status_label ); ?>
									</span>
								<?php endif; ?>
							</div>
						</a>
					<?php endif; ?>

					<div class="ebmp-card-body">

						<h2 class="ebmp-card-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<div class="ebmp-card-meta">
							<?php if ( ! empty( $formatted_date ) ) : ?>
								<div class="ebmp-card-meta-row">
									<span class="ebmp-card-meta-icon">&#128197;</span>
									<span><?php echo esc_html( $formatted_date ); ?></span>
									<?php if ( ! empty( $formatted_time ) ) : ?>
										<span class="ebmp-card-meta-sep">&bull;</span>
										<span><?php echo esc_html( $formatted_time ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $location ) ) : ?>
								<div class="ebmp-card-meta-row">
									<span class="ebmp-card-meta-icon">&#128205;</span>
									<span><?php echo esc_html( wp_trim_words( $location, 8 ) ); ?></span>
								</div>
							<?php endif; ?>

							<?php if ( '' !== $available_seats && false !== $available_seats ) : ?>
								<div class="ebmp-card-meta-row">
									<span class="ebmp-card-meta-icon">&#127903;</span>
									<span>
										<?php
										echo esc_html(
											sprintf(
												/* translators: %d: number of seats */
												_n( '%d seat available', '%d seats available', absint( $available_seats ), 'event-booking-manager-pro' ),
												absint( $available_seats )
											)
										);
										?>
									</span>
								</div>
							<?php endif; ?>
						</div><!-- .ebmp-card-meta -->

						<?php if ( ! empty( get_the_excerpt() ) ) : ?>
							<p class="ebmp-card-excerpt"><?php the_excerpt(); ?></p>
						<?php endif; ?>

						<a href="<?php the_permalink(); ?>" class="ebmp-card-btn ebmp-status-btn-<?php echo esc_attr( $booking_status ); ?>">
							<?php
							if ( 'open' === $booking_status ) {
								esc_html_e( 'View &amp; Book', 'event-booking-manager-pro' );
							} elseif ( 'cancelled' === $booking_status ) {
								esc_html_e( 'View Event', 'event-booking-manager-pro' );
							} else {
								esc_html_e( 'View Details', 'event-booking-manager-pro' );
							}
							?>
						</a>

					</div><!-- .ebmp-card-body -->

				</article>

			<?php endwhile; ?>
		</div><!-- .ebmp-events-grid -->

		<div class="ebmp-pagination">
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => '&larr; ' . __( 'Previous', 'event-booking-manager-pro' ),
					'next_text' => __( 'Next', 'event-booking-manager-pro' ) . ' &rarr;',
				)
			);
			?>
		</div>

	<?php else : ?>

		<div class="ebmp-no-events">
			<p><?php esc_html_e( 'No upcoming events found. Check back soon!', 'event-booking-manager-pro' ); ?></p>
		</div>

	<?php endif; ?>

</div><!-- .ebmp-archive-wrap -->

<?php get_footer(); ?>
