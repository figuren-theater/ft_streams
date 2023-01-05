<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://carsten-bach.de
 * @since      1.0.0
 *
 * @package    Ft_streams
 * @subpackage Ft_streams/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ft_streams
 * @subpackage Ft_streams/includes
 * @author     Carsten Bach <mail@carsten-bach.de>
 */
class Ft_streams {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Ft_streams_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'FT_STREAMS_VERSION' ) ) {
			$this->version = FT_STREAMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'ft_streams';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Ft_streams_Loader. Orchestrates the hooks of the plugin.
	 * - Ft_streams_i18n. Defines internationalization functionality.
	 * - Ft_streams_Admin. Defines all hooks for the admin area.
	 * - Ft_streams_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ft_streams-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ft_streams-i18n.php';

		/**
		 * The class responsible for registration 
		 * of custom post_types and taxonomies.
		 */
#		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ft_streams-posttypes-taxonomies.php';


		/**
		 * The class responsible for defining modifications for needed plugins
		 * of the website.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'plugin_mods/class-ft_streams-plugin_mods.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'plugin_mods/class-ft_streams-pm-eo.php';


		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-ft_streams-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-ft_streams-public.php';

		$this->loader = new Ft_streams_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ft_streams_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Ft_streams_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ft_streams_Admin( $this->get_plugin_name(), $this->get_version() );

#		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
#		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'publish_event', $plugin_admin, 'notify_stream_author_on_publish',10,2 );


#		$plugin_cpts = new Ft_streams_Posttypes_and_Taxonomies();

#		$this->loader->add_action( 'init', $plugin_cpts, 'ft_stream_post_type' );
#		$this->loader->add_filter( 'post_updated_messages', $plugin_cpts, 'ft_stream_updated_messages' );

#		$this->loader->add_action( 'init', $plugin_cpts, 'ft_stream_tag_taxonomy' );


		$plugin_mods = new Ft_streams_plugin_mods();

		$this->loader->add_filter('cf7_2_post_filter-event-title',$plugin_mods,'filter_ft_stream_title',10,3);

		$this->loader->add_filter( 'cf7_2_post_filter-event-slug',$plugin_mods,'filter_ft_stream_slug',10,3);

		$this->loader->add_filter( 'wp_unique_post_slug', $plugin_mods, 'filter_ft_stream_unique_post_slug', 10, 4 );



		$this->loader->add_filter( 'cf7_2_post_filter-event-author',$plugin_mods,'filter_ft_stream_author',10,3);

		$this->loader->add_filter('cf7_2_post_filter-event-editor',$plugin_mods,'filter_ft_stream_editor',10,3);

#		$this->loader->add_filter('cf7_2_post_filter-event-ft_streams_name',$plugin_mods,'filter_ft_stream_ft_streams_name',10,3);

#		$this->loader->add_filter('cf7_2_post_filter-event-ft_streams_twitter',$plugin_mods,'filter_ft_stream_ft_streams_twitter',10,3);
#		$this->loader->add_filter('cf7_2_post_filter-event-ft_streams_fb',$plugin_mods,'filter_ft_stream_ft_streams_fb',10,3);

#		$this->loader->add_filter('cf7_2_post_filter-event-ft_streams_email',$plugin_mods,'filter_ft_stream_ft_streams_email',10,3);


		$this->loader->add_filter('cf7_2_post_filter-event-ft_streams_url',$plugin_mods,'filter_ft_stream_ft_streams_url',10,3);


		$this->loader->add_filter( 'cf7_2_post_filter-event-ft_streams_start_date',$plugin_mods,'filter_ft_stream_ft_streams_start_date',10,3);
#		$this->loader->add_filter( 'cf7_2_post_draft_skips_validation', $plugin_mods,'force_validation', 10, 2);
		$this->loader->add_filter( 'cf7_2_post_status_event', $plugin_mods,'post_status_for_submitted_ft_stream',10,3);
#		$this->loader->add_filter( 'cf7_2_post_filter-ft_stream-ft_streams_twitter',$plugin_mods,'filter_ft_stream_ft_streams_twitter',10,3);

		$this->loader->add_action('cf7_2_post_form_submitted_to_event', $plugin_mods, 'new_ft_stream_mapped',10,3);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Ft_streams_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
#		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );


		$this->loader->add_action( 'wp_head', $plugin_public, 'remove_twentytwenty_post_meta', 100 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ft_streams_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
