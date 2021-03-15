<?php

class Flight_Prices_Monitor_Admin {

	private $name;
	private $version;

	// Initializes the class and set its properties
	public function __construct( $name, $version ) {
		$this->name = $name;
		$this->version = $version;
	}

	// Registers the stylesheets for the admin area
	public function enqueue_styles() {
		wp_enqueue_style( 
			$this->name, 
			plugin_dir_url( __FILE__ ) . 'css/fpm-admin.css', 
			array(), $this->version, 'all' );
	}

	// Registers the JavaScript for the admin area
	public function enqueue_scripts() {
		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/fpm-admin.js', array( 'jquery' ), $this->version, false );
	}

	// Action for the settings page
	public function add_settings_page() {
		add_options_page( 'Flymon Options', 'Flymon', 'manage_options', $this->get_slug(), array($this, 'render_settings_page') );
	}

	// Action for configuring the settings on the page
	public function configure()
    {
        // Register settings
        register_setting($this->get_slug(), 'flymon_kiwi_apikey');
        register_setting($this->get_slug(), 'flymon_kiwi_affil_id');
        register_setting($this->get_slug(), 'flymon_kiwi_market');
        register_setting($this->get_slug(), 'flymon_cache_hours');
		
		add_settings_section(
			$this->get_slug() . '-sectionB',
			'Internal settings',
			null,
			$this->get_slug()
		);

		add_settings_field(
			$this->get_slug() . '_cache_hours', 
			'Cache time in hours',
			array($this, 'render_option_cache'), 
			$this->get_slug(),
			$this->get_slug() . '-sectionB'
		);

        add_settings_section(
            $this->get_slug() . '-sectionA',
            'Kiwi.com affiliate settings',
            null,
            $this->get_slug()
        );

        add_settings_field(
            $this->get_slug() . '_kiwi_apikey', 
            'API key',
            array($this, 'render_option_kiwi_apikey'), 
            $this->get_slug(),
			$this->get_slug() . '-sectionA'
        );

		add_settings_field(
            $this->get_slug() . '_kiwi_affil_id', 
            'AffilID',
            array($this, 'render_option_kiwi_affil_id'), 
            $this->get_slug(),
			$this->get_slug() . '-sectionA'
        );

		add_settings_field(
            $this->get_slug() . '_kiwi_market', 
            'Partner market',
            array($this, 'render_option_kiwi_market'), 
            $this->get_slug(),
			$this->get_slug() . '-sectionA'
        );
    }

	// Renders the plugin settings page template
	function render_settings_page() {
		$this->render_template('fpm-admin-page');
	}

	// Functions to render the plugin individual settings
	function render_option_kiwi_apikey() {
		$this->render_template('option-apikey');
	}
	function render_option_kiwi_affil_id() {
		$this->render_template('option-affil-id');
	}
	function render_option_kiwi_market() {
		$this->render_template('option-market');
	}
	function render_option_cache() {
		$this->render_template('option-cache');
	}

    // Renders the given template if it's readable
    private function render_template($template)
    {
        $template_path = $this->get_template_path() . '/' . $template . '.php';
        if (!is_readable($template_path)) {
            return;
        }
        include $template_path;
    }

	// Returns the partials path for the admin page
	function get_template_path() {
		return plugin_dir_path( __FILE__ ) . 'partials';
	}

	// Returns the slug used by the admin page
	function get_slug() {
		return 'flymon'; // Could use $this->name
	}
}
