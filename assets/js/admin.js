/**
 * Event Booking Manager Pro — Admin JavaScript
 *
 * Loaded only on the event post type edit screen.
 * Handles any client-side enhancements for the meta box.
 *
 * @package TPots\EventBooking
 */

( function ( $ ) {
	'use strict';

	/**
	 * Initialise meta box enhancements on DOM ready.
	 */
	$( document ).ready( function () {

		// Highlight the booking status select with a colour indicator.
		var $statusSelect = $( '#ebmp_booking_status' );

		/**
		 * Apply a CSS class to the select based on the chosen value
		 * so editors get a quick visual cue of the current status.
		 */
		function applyStatusClass() {
			$statusSelect
				.removeClass( 'status-open status-closed status-cancelled' )
				.addClass( 'status-' + $statusSelect.val() );
		}

		if ( $statusSelect.length ) {
			applyStatusClass();
			$statusSelect.on( 'change', applyStatusClass );
		}

	} );

}( jQuery ) );
