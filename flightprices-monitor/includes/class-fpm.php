<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */

 class Flight_Prices_Monitor {

	// Maintains and registers all hooks for the plugin
	protected $loader;

	// The unique identifier of this plugin
	protected $name;

	// The current version of the plugin
	protected $version;

	// Configuration storage
	protected $config;

	/**
	 * Sets the plugin name and the plugin version that can be used throughout the plugin.
	 * Loads the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 */
	public function __construct() {
		if ( defined( 'FPM_VERSION' ) ) {
			$this->version = FPM_VERSION;
		} else {
			$this->version = '0.1';
		}
		$this->name = 'flight-prices-monitor';

		$this->load_config();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	// Loads the minimal configuration; the rest is loaded by class constructors when needed
	private function load_config() {
		$this->config = [];
	}

	// Loads the required dependencies for this plugin.
	private function load_dependencies() {

		// The class responsible for orchestrating the actions and filters of the core plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fpm-loader.php';

		// The class responsible for defining internationalization functionality of the plugin.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fpm-i18n.php';

		// The class responsible for defining all actions that occur in the admin area.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fpm-admin.php';

		// The class responsible for defining all actions that occur in the public-facing side of the site.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fpm-public.php';

		$this->loader = new Flight_Prices_Monitor_Loader();

	}

	// Defines the locale for this plugin for internationalization.
	private function set_locale() {
		$plugin_i18n = new Flight_Prices_Monitor_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	// Registers all of the hooks related to the admin area functionality of the plugin.
	private function define_admin_hooks() {

		$plugin_admin = new Flight_Prices_Monitor_Admin( 
			$this->get_name(), 
			$this->get_version()
		);

		// JS and CSS
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Settings page-related
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'configure' );

	}

	// Registers all of the hooks related to the public-facing functionality of the plugin.
	private function define_public_hooks() {

		$plugin_public = new Flight_Prices_Monitor_Public( 
			$this->get_name(), 
			$this->get_version(), 
			$this->get_config()
		);

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Register the shortcodes
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

		// Add AJAX handlers
		$this->loader->add_action( 'init', $plugin_public, 'add_ajax_handlers' );

	}

	// Runs the loader to execute all of the hooks with WordPress.
	public function run() {
		$this->loader->run();
	}

	// Getters for name, loader, version etc
	
	public function get_config() {
		return $this->config;
	}

	public function get_name() {
		return $this->name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}

}
