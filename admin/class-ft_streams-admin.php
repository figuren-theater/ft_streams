<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://carsten-bach.de
 * @since      1.0.0
 *
 * @package    Ft_streams
 * @subpackage Ft_streams/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ft_streams
 * @subpackage Ft_streams/admin
 * @author     Carsten Bach <mail@carsten-bach.de>
 */
class Ft_streams_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ft_streams_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ft_streams_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ft_streams-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ft_streams_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ft_streams_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ft_streams-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function notify_stream_author_on_publish($post_id, $post) {

		//
		if ( !has_term( 'streams', 'event-category', $post ) )
			return;

		//
		if ( 'yes' !== $post->_cf7_2_post_form_submitted )
			return;

		// prepare data
		$stream_author = $post->_ft_streams_author;

		//
		if ( !is_array($stream_author) || empty($stream_author) ) 
			return;

		// pre-filled mailchimp subscribe link
		$subscribe_to_mailchimp = sprintf(
			'https://theater.us19.list-manage.com/subscribe?u=4abd4a45d2f5b362124b7013b&id=afd019ab7c&MERGE0=%1$s&MERGE3=%2$s',
			$stream_author['email'],
			$stream_author['name']
		);

		//
		$share = do_shortcode( '[ft_social_share url="'.get_permalink($post).'" title="'.get_the_title($post).'"]' );

		//
		$date = get_the_date( '', $post );


		// prepare email
		$subject = "[figuren.theater] Dein Stream kann beginnen: ".$post->post_title." ist online";
		$message = "<h2>Hallo ".$stream_author['name'].",</h2>
<strong>Dein Streaming-Angebot wurde soeben auf <em>figuren.theater</em> veröffentlicht.</strong> \"<a href='".get_permalink( $post_id )."'>".$post->post_title."</a>\" kann also starten.

Bis die Scheinwerfer wirklich an gehen ist noch etwas Zeit ...
<h3>Zeit allen Bescheid zu geben:</h3>
Teile die URL zu Deinem Streaming-Angebot mit Deinen Freundinnen, Fans & Kollegen und lade alle ein, sich den Termin - am Besten sofort - im Kalender zu speichern.
".$share."
Wir wünschen Dir viele Zuschauer für Deinen Stream und verbleiben mit
<strong>Toi Toi Toi</strong> & bis bald!

<a href='https://meta.figuren.theater/crew/'>Deine Crew</a> bei <em>figuren.theater</em>";

		// 
		if (0 == $stream_author['nl']) {
			$message .= "

P.S. <em>figuren.theater</em> will viel mehr als nur Streams zu veröffentlichen. Bleibe über den aktuellen Stand der Entwicklung auf dem Laufenden und erfahre per Email von den nächsten Schritten auf <em>figuren.theater</em>.

<a href='".$subscribe_to_mailchimp."'>Sei von Anfang an dabei</a>, denn es wird aufregend!";
		}

		//
		$message .= "

<small><em>Du erhältst diese E-Mail, weil Du am ".$date." ein Streaming-Angebot eingetragen hast, dass soeben veröffentlicht wurde.</em></small>";

		// SEND
		wp_mail($stream_author['email'], $subject, $message);


		// make sure this notification is only send once
		// delete the meta, we check against for the allowance
		delete_post_meta( $post_id, '_cf7_2_post_form_submitted' );
	}

}
