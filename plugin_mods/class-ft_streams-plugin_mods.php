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
class Ft_streams_plugin_mods {

	public function filter_ft_stream_title($value, $post_id, $form_data){
		//$value is the post field value to return, by default it is empty. If you are filtering a taxonomy you can return either slug/id/array.  in case of ids make sure to cast them integers.(see https://codex.wordpress.org/Function_Reference/wp_set_object_terms for more information.)
		//$post_id is the ID of the post to which the form values are being mapped to
		// $form_data is the submitted form data as an array of field-name=>value pairs
		return sanitize_text_field( $form_data['ft_streams_title'] );
	}


	public function filter_ft_stream_slug($value, $post_id, $form_data){
		//$value is the post field value to return, by default it is empty. If you are filtering a taxonomy you can return either slug/id/array.  in case of ids make sure to cast them integers.(see https://codex.wordpress.org/Function_Reference/wp_set_object_terms for more information.)
		//$post_id is the ID of the post to which the form values are being mapped to
		// $form_data is the submitted form data as an array of field-name=>value pairs

#		$_combine_title_and_date = $form_data['ft_streams_title'].'-'.$form_data['ft_streams_start_date'];

		return wp_unique_post_slug( 
#			sanitize_title( $form_data['ft_streams_title'].'-'.$form_data['ft_streams_start_date'], $post_id ), 
#			sanitize_title_with_dashes( $_combine_title_and_date ), 
			sanitize_title_with_dashes( $form_data['ft_streams_title'].'-'.$form_data['ft_streams_name'] ).'-'.$form_data['ft_streams_start_date'], 
			$post_id, 
			'publish', 
			'event', 
			0 
		);
	}
	// use the same logic for updating a post manually and its URL accordingly
	private function get__ft_stream_unique_post_slug( $slug, $post_ID, $post_status, $post_type ) {
		# code...wp_unique_post_slug()
	}


	public function filter_ft_stream_unique_post_slug( $slug, $post_ID, $post_status, $post_type ) {

		global $wpdb;

		if ( 'event' != $post_type )
			return $slug;

		//
		if ( !has_term( 'streams', 'event-category', $post_ID ) )
			return $slug;

		// at this point
		// we throw away the 'old' existing post slug
		// bye bye $slug

		// prepare data
		$_title = get_the_title( $post_ID );
		$_author = get_post_meta( $post_ID, '_ft_streams_author', true );
		$_date = eo_get_schedule_start( 'd-m-Y', $post_ID );

#		$slug = sanitize_title_with_dashes( 
		// make sure all of this works well with 'äöüß' etc.
		$slug = sanitize_title( 
			$_title.'-'.
			$_author['name'] .'-'.
			$_date
		);

		//Lets make sure the slug is really unique:
		$check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND ID != %d LIMIT 1";
		$post_name_check = $wpdb->get_var( $wpdb->prepare( $check_sql, $slug, $post_ID ) );

		if ( $post_name_check ) {
			$suffix = 2;

			do {
				$alt_post_name = substr ($slug, 0, 200 - ( strlen( $suffix ) + 1 ) ) . "--$suffix";
				$post_name_check = $wpdb->get_var( $wpdb->prepare($check_sql, $alt_post_name, $post_ID ) );
				$suffix++;
			} while ( $post_name_check );

			$slug = $alt_post_name;
		}


		return $slug;
	}



	public function filter_ft_stream_author($value, $post_id, $form_data){
		//$value is the post field value to return, by default it is empty. If you are filtering a taxonomy you can return either slug/id/array.  in case of ids make sure to cast them integers.(see https://codex.wordpress.org/Function_Reference/wp_set_object_terms for more information.)
		//$post_id is the ID of the post to which the form values are being mapped to
		// $form_data is the submitted form data as an array of field-name=>value pairs

		//
		$_ft_bot = get_user_by( 'login', 'ft_bot' );
		return $_ft_bot->ID;
	}


	public function filter_ft_stream_editor($value, $post_id, $form_data){
		//$value is the post field value to return, by default it is empty. If you are filtering a taxonomy you can return either slug/id/array.  in case of ids make sure to cast them integers.(see https://codex.wordpress.org/Function_Reference/wp_set_object_terms for more information.)
		//$post_id is the ID of the post to which the form values are being mapped to
		// $form_data is the submitted form data as an array of field-name=>value pairs
		return wp_filter_nohtml_kses( $form_data['ft_streams_desc'] );
	}


	public function filter_ft_stream_ft_streams_url($value, $post_id, $form_data){
		//$value is the post field value to return, by default it is empty. If you are filtering a taxonomy you can return either slug/id/array.  in case of ids make sure to cast them integers.(see https://codex.wordpress.org/Function_Reference/wp_set_object_terms for more information.)
		//$post_id is the ID of the post to which the form values are being mapped to
		// $form_data is the submitted form data as an array of field-name=>value pairs
		return esc_url_raw( $form_data['ft_streams_url'], array('http','https') );
	}


	public function filter_ft_stream_ft_streams_start_date($value, $post_id, $form_data){
		//$value is the post field value to return, by default it is empty. If you are filtering a taxonomy you can return either slug/id/array.  in case of ids make sure to cast them integers.(see https://codex.wordpress.org/Function_Reference/wp_set_object_terms for more information.)
		//$post_id is the ID of the post to which the form values are being mapped to
		// $form_data is the submitted form data as an array of field-name=>value pairs

		// prepare some fallbacks
		$form_data['ft_streams_start_hour'] = (!isset($form_data['ft_streams_start_hour']) || empty($form_data['ft_streams_start_hour'])) ? '00' : $form_data['ft_streams_start_hour'];
		$form_data['ft_streams_start_min'] = (!isset($form_data['ft_streams_start_min']) || empty($form_data['ft_streams_start_min'])) ? '00' : $form_data['ft_streams_start_min'];
		$form_data['ft_streams_end_hour'] = (!isset($form_data['ft_streams_end_hour']) || empty($form_data['ft_streams_end_hour'])) ? '00' : $form_data['ft_streams_end_hour'];
		$form_data['ft_streams_end_min'] = (!isset($form_data['ft_streams_end_min']) || empty($form_data['ft_streams_end_min'])) ? '00' : $form_data['ft_streams_end_min'];


		// Add leading zero
		$form_data['ft_streams_start_hour'] = str_pad($form_data['ft_streams_start_hour'], 2, '0', STR_PAD_LEFT);
		$form_data['ft_streams_end_hour']   = str_pad($form_data['ft_streams_end_hour'],   2, '0', STR_PAD_LEFT);


		$event_data__start = $form_data['ft_streams_start_date'].' '.$form_data['ft_streams_start_hour'].':'.$form_data['ft_streams_start_min'].':00';


		// use start date as fallback for end date
		$form_data['ft_streams_end_date'] = (!isset($form_data['ft_streams_end_date']) || empty($form_data['ft_streams_end_date'])) ? $form_data['ft_streams_start_date'] : $form_data['ft_streams_end_date'];


		$event_data__end   = $form_data['ft_streams_end_date'].' '.($form_data['ft_streams_end_hour']).':'.$form_data['ft_streams_end_min'].':00';



		$event_data = array(
		     'start'     => new DateTime( $event_data__start, eo_get_blog_timezone() ),
		     'end'       => new DateTime( $event_data__end,   eo_get_blog_timezone() ),
		     'until'     => new DateTime( $event_data__start, eo_get_blog_timezone() ),
		#     'frequency' => 4,
		     'all_day'   => 0,
		     'schedule'  => 'once',
		   );



		/**
		* This functions updates a post of event type, with data given in the $post_data
		* and event data given in $event_data. Returns the post_id.
		*
		* Triggers {@see `eventorganiser_save_event`} passing event (post) ID
		*
		* The event data array can contain
		*
		* * `schedule` => (custom | once | daily | weekly | monthly | yearly)  -- specifies the recurrence pattern
		* * `schedule_meta` =>
		*   * For monthly schedules,
		*      * (string) BYMONTHDAY=XX to repeat on XXth day of month, e.g. BYMONTHDAY=01 to repeat on the first of every month.
		*      * (string) BYDAY=ND. N= 1|2|3|4|-1 (first, second, third, fourth, last). D is day of week SU|MO|TU|WE|TH|FR|SA. E.g. BYDAY=2TU (repeat on second tuesday)
		*   * For weekly schedules,
		*      * (array) Days to repeat on: (SU,MO,TU,WE,TH,FR,SA). e.g. set to array('SU','TU') to repeat on Tuesdays & Sundays.
		*      * Can be left blank to repeat weekly from the start date.
		* * `frequency` => (int) positive integer, sets frequency of recurrence (every 2 days, or every 3 days etc)
		* * `all_day` => 1 if its an all day event, 0 if not
		* * `start` =>  start date (of first occurrence)  as a datetime object
		* * `end` => end date (of first occurrence)  as a datetime object
		* * `until` =>  **START** date of last occurrence (or upper-bound thereof) as a datetime object
		* * `schedule_last` =>  Alias of until. Deprecated 2.13.0, use until.
		* * `number_occurrences` => Instead of specifying `until` you can specify the number of occurrence a recurring event should have.
		* This is only used if `until` is not, and for daily, weekly, monthly or yearly recurring events.
		* * `include` => array of datetime objects to include in the schedule
		* * `exclude` => array of datetime objects to exclude in the schedule
		*/
		eo_update_event( $post_id, $event_data, array() );
#		eo_insert_event( array(), $event_data );

/**/

		// clean up
		add_action( 'cf7_2_post_form_submitted_to_event', function($post_id, $cf7_form_data, $cf7form_key){
			// do not keep empty values
			delete_post_meta( $post_id, 'ft_streams_start_date' );
		},10,3 );

		return false;
	}




	/**
	* Function to change the post status of saved/submitted posts.
	* @param string $status the post status, default is 'draft'.
	* @param string $ckf7_key unique key to identify your form.
	* @param array $submitted_data complete set of data submitted in the form as an array of field-name=>value pairs.
	* @return string a valid post status ('publish'|'draft'|'pending'|'trash')
	*/
	public function post_status_for_submitted_ft_stream($status, $ckf7_key, $submitted_data){
		/*The default behaviour is to save post to 'draft' status.  If you wish to change this, you can use this filter and return a valid post status: 'publish'|'draft'|'pending'|'trash'*/
#		if('streams_copy' == $ckf7_key){
		if('streaming' == $ckf7_key){
			return 'pending';
		} else {
			return $status;
		}
	}



	/**
	* Function to take further action once form has been submitted and saved as a post.  Note this action is only fired for submission which has been submitted as opposed to saved as drafts.
	* @param string $post_id new post ID to which submission was saved.
	* @param array $cf7_form_data complete set of data submitted in the form as an array of field-name=>value pairs.
	* @param string $cf7form_key unique key to identify your form.
	*/
	public function new_ft_stream_mapped($post_id, $cf7_form_data, $cf7form_key){
#		if('streams_copy' == $cf7form_key){
		if('streaming' == $cf7form_key){


			/* SAVE URLs
			 *
			 */
			$cleaned_urls = array();
			$input_count = (int)$cf7_form_data['_wpcf7_groups_count']['ft_streams_urls'];
			for ($i=1; $i <= $input_count; $i++) { 
				// replace all http with https to prevent mixed content errors
				$_url = str_replace('http:', 'https:', $cf7_form_data['ft_streams_urls__'.$i]);
				// validate user input
				$cleaned_urls[] = esc_url_raw( $_url, array('https') );
			}

			// Remove empty elements
			$cleaned_urls = array_filter($cleaned_urls);

			//
			update_post_meta( $post_id, 'ft_streams_urls', $cleaned_urls );


			/* SAVE EMAIL, PRIVACY & NEWSLETTER OPTIONS
			 *
			 */
			$author_meta = array(
				'email' => sanitize_email( $cf7_form_data['ft_streams_email'] ),
				'name' => sanitize_text_field( $cf7_form_data['ft_streams_name'] ),
#				'nl' => ($cf7_form_data['ft_streams_newsletter']) ? 1 : 0,
				'nl' => (int)$cf7_form_data['ft_streams_newsletter'],
#				'privacy' => ($cf7_form_data['ft_streams_dsgvo']) ? 1 : 0
				'privacy' => (int)$cf7_form_data['ft_streams_dsgvo']
			);
			//
			update_post_meta( $post_id, '_ft_streams_author', $author_meta );


			/* SAVE EVENT-CATEGORY
			 *
			 *
			 * @param int              $object_id The object to relate to.
			 * @param string|int|array $terms     A single term slug, single term id, or array of either term slugs or ids.
			 *                                    Will replace all existing related terms in this taxonomy. Passing an
			 *                                    empty value will remove all related terms.
			 * @param string           $taxonomy  The context in which to relate the term to the object.
			 * @param bool             $append    Optional. If false will delete difference of terms. Default false.
			 * @return array|WP_Error Term taxonomy IDs of the affected terms or WP_Error on failure.
			 */
			switch ($cf7_form_data['ft_streams_type'][0]) {
				case 'Stream':
					$streams_sub_category = 'on-demand';
					break;
				case 'Live':
				default:
					$streams_sub_category = 'live';
					break;
			}
			wp_set_object_terms( $post_id, array('streams',$streams_sub_category), 'event-category' );
		}
	}

} // CLASS Ft_streams_plugin_mods
