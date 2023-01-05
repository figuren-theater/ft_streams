<?php

/**
 * Define the modification for needed plugins used by figuren.theater
 *
 * @link       https://carsten-bach.de/
 * @since      1.0.0
 *
 * @package    Ft_streams
 * @subpackage Ft_streams/plugin_mods
 */

/**
 * Define the modification for needed plugins used by figuren.theater.
 *
 * @since      1.0.0
 * @package    Ft_streams
 * @subpackage Ft_streams/plugin_mods
 * @author     Carsten Bach <mail@carsten-bach.de>
 */
class Ft_streams_pm_eo {


} // CLASS Ft_streams_pm_eo


/**
 * Returns a the url which adds a particular occurrence of an event to
 * a google calendar.
 *
 * Returns an url which adds a particular occurrence of an event to a Google calendar. This function can only be used inside the loop.
 * An entire series cannot be added to a Google calendar - however users can subscribe to your events. Please note that, unlike
 * subscribing to events, changes made to an event will not be reflected on an event added to the Google calendar.
 *
 * ### Examples
 * Add a 'add this event to Google' link:
 * <code>
 *    <?php
 *      //Inside the loop
 *      $url = eo_get_add_to_google_link();
 *      echo '<a href="'.esc_url($url).'"> Add to Google </a>';
 *      ?>
 * </code>
 * @since 2.3
 * @param int $post_id Post ID of the event.
 * @param int $occurrence_id The occurrence ID.
 * @return string Url which adds event to a google calendar
 */
function ft_streams_get_add_to_google_link( $event_id = 0, $occurrence_id = 0 ){

	global $post;
	$event = $post;

	$event_id = (int) ( $event_id ? $event_id : get_the_ID() );
	$occurrence_id = (int) ( !$occurrence_id && isset( $event->occurrence_id )  ? $event->occurrence_id : $occurrence_id );

	$post = get_post( $event_id );

	if( !$occurrence_id || !$post || 'event' != get_post_type( $post ) ){
		wp_reset_postdata();
		return false;
	}

	setup_postdata( $post );

	$start = clone eo_get_the_start( DATETIMEOBJ, $event_id, $occurrence_id );
	$end   = clone eo_get_the_end( DATETIMEOBJ, $event_id, $occurrence_id );

	if( eo_is_all_day() ):
		$end->modify( '+1 second' );
		$format = 'Ymd';
	else:
		$format = 'Ymd\THis\Z';
		$start->setTimezone( new DateTimeZone( 'UTC' ) );
		$end->setTimezone( new DateTimeZone( 'UTC' ) );
	endif;

	/**
	 * @ignore
	 */
	$excerpt = apply_filters( 'the_excerpt_rss', get_the_excerpt() );
/*
	$venue    = false;
	$venue_id = eo_get_venue();
	if ( $venue_id ) {
		$venue = eo_get_venue_name( $venue_id ) . ", " . implode( ', ', eo_get_venue_address( $venue_id ) );
	}*/

	$stream_url = $post->ft_streams_url;

	$url = add_query_arg( array(
			'text'     => get_the_title(),
			'dates'    => $start->format( $format ) . '/' . $end->format( $format ),
			'details'  => esc_html( $excerpt ),
			'sprop'    => get_bloginfo( 'name' ),
			'location' => $stream_url,
	), 'http://www.google.com/calendar/event?action=TEMPLATE' );

	wp_reset_postdata();
	return esc_url_raw( $url );
}


