<?php

// TODO: Better error reporting (200 instead of 500), 
class Flight_Prices_Monitor_Public {

	private $name;
	private $version;
	private $config; // Configuration array, received on init

	public function __construct( $name, $version, $config ) {
		$this->name = $name;
		$this->version = $version;
		$this->config = $config;
		$this->config['affilid'] = get_option('flymon_kiwi_affil_id', '');
	}

	// Registers the stylesheets for the public-facing side of the site
	public function enqueue_styles() {
		wp_enqueue_style( 
			$this->name, 
			plugin_dir_url( __FILE__ ) . 'css/fpm-public.css', 
			array(), $this->version, 'all' );
	}

	// Registers the JavaScript for the public-facing side of the site
	public function enqueue_scripts() {
		wp_enqueue_script( 
			$this->name, 
			plugin_dir_url( __FILE__ ) . 'js/fpm-public.js', 
			array( 'jquery' ), $this->version, false );
	
		// Also sets the AJAX URL on front end; preferred to using wp_localize_script()
		wp_add_inline_script( 
			$this->name, 
			'const WP_FLYMON = ' . json_encode( array( 'ajaxUrl' => admin_url('admin-ajax.php') ) ), 
			'before' 
		);
	}
        
	// Registers shortcodes for the public-facing side of the site
	public function register_shortcodes() {
		add_shortcode('trip_price', array ($this, 'add_price_widget'));
	}

	// Constructor for fpm_price shortcode; returns HTML string
	function add_price_widget($atts) {
		
		// Let's set defaults for missing values
		$a = shortcode_atts(array(
			'from' => '',
			'to' => '',
			'earliest' => 'today',
			'latest' => '+3 months',
			'min_days' => '7',
			'max_days' => '14',
			'max_stops' => '0',
			'transport' => 'aircraft',
			'currency' => 'EUR',
			'locale' => 'en',
			'deeplink' => 'search',
		), $atts);

		// Validate the input and set the rest of the defaults
		$a['from'] = filter_var($a['from'], FILTER_SANITIZE_STRING);
		$a['to'] = filter_var($a['to'], FILTER_SANITIZE_STRING);

		$earliest = strtotime($a['earliest']);
		$a['earliest'] = $earliest ? date('d/m/Y', $earliest) : date('d/m/Y');

		$latest = strtotime($a['latest']);
		$a['latest'] = $latest ? date('d/m/Y', $latest) : date('d/m/Y', strtotime("+3 months"));

		$a['min_days'] = filter_var($a['min_days'], FILTER_SANITIZE_NUMBER_INT);
		$a['max_days'] = filter_var($a['max_days'], FILTER_SANITIZE_NUMBER_INT);            

		if ( !in_array($a['currency'], KIWI_API::CURRENCIES) ) $a['currency'] = 'EUR';
		if ( !in_array($a['locale'], KIWI_API::LOCALES) ) $a['locale'] = 'en';

		$a['transport'] = str_replace(' ', '', $a['transport']);
		$vehicles = explode(',', $a['transport']);
		$unlisted = array_diff($vehicles, ['aircraft', 'bus', 'train']);
		if ( count($unlisted) !== 0 )
			$a['transport'] = 'aircraft';

		if ( !in_array($a['deeplink'], ['search', 'booking']) ) $a['deeplink'] = 'search';

		// NOTE: must be called inside init or any subsequent hook; register_shortcodes() is the case
		$ajaxNonce = wp_create_nonce('flymon-price-request'); 

		// Return HTML widget replacing the fpm_price shortcode
		$data = 'data-fly_from="' . $a['from']
				. '" data-fly_to="' . $a['to']
				. '" data-date_from="' . $a['earliest']
				. '" data-date_to="' . $a['latest']
				. '" data-nights_in_dst_from="' . $a['min_days']
				. '" data-nights_in_dst_to="' . $a['max_days']
				. '" data-max_stopovers="' . $a['max_stops']
				. '" data-curr="' . $a['currency']
				. '" data-locale="' . $a['locale']
				. '" data-vehicle_type="' . $a['transport']
				. '" data-affilid="' . $this->config['affilid']
				. '" data-deeplink_type="' . $a['deeplink']
				. '" data-security="' . $ajaxNonce
				. '"';
		
		// Mind the ellipsis dots in one line
		return '<span class="flymon-tag" ' . $data . '><span class="flymon-tag__dot1">.</span><span class="flymon-tag__dot2">.</span><span class="flymon-tag__dot3">.</span></span>';
	}

	// Adds AJAX handlers for the public-facing side of the site.
	public function add_ajax_handlers() {
		add_action( 'wp_ajax_nopriv_price', array ($this, 'get_price') );
		add_action( 'wp_ajax_price', array ($this, 'get_price') );
	}

	// Handler for the AJAX price request; returns the price JSON
	function get_price() {
		$query = $_POST;
		$response = [];
		check_ajax_referer('flymon-price-request', 'security');
		
		unset($query['action']);
		unset($query['security']);
		unset($query['deeplink_type']); // we return the booking deeplink anyway and let front-end app decide
		
		$required = ['fly_from', 'fly_to', 'date_from', 'date_to'];
		foreach ( $required as $term ) {
			if ( !isset($query[$term]) ) $this->throw_error(400, "'{$term}' not defined");
		}

		$search_string = implode('|', $query);
		$search_id = wp_hash($search_string);
		// delete_transient($search_id); // for testing
		$price = json_decode(get_transient($search_id));

		if ( !$price ) {
			// unset($query); // to send an incorrect query to Kiwi.com for testing
			$config = $this->load_api_config($this->config);
			$kiwi_api = new KIWI_API($config);
			$price = $kiwi_api->request_price($query, $config);
			if ( !$price ) $this->throw_error(500, 'Could not retrieve the price from Kiwi, unknown error');
			
			$cache_time = $config['cache_hours'] * HOUR_IN_SECONDS;
			set_transient($search_id, json_encode($price, JSON_UNESCAPED_SLASHES), $cache_time);
		};

		$response = json_encode(['success' => true, 'data' => $price], JSON_UNESCAPED_SLASHES);
		echo $response;
		wp_die(); // to terminate immediately and return a proper response
	}

	// Gets the settings required to call the API and save result
	function load_api_config($base_config) {
		$config = $base_config;
		$config['apikey'] = get_option('flymon_kiwi_apikey');
		if ( ! $config['apikey'] ) $this->throw_error(500, 'Kiwi API key is not set');
	
		$config['partner_market'] = get_option('flymon_kiwi_market', '');
		$config['cache_hours'] = intval(get_option('flymon_cache_hours', 24));
		return $config;
	}

	// Responds with the error code + JSON {"success": "false", "data": $message} and stops
	function throw_error(int $code, string $message) {
		wp_send_json_error($message, $code);
  		wp_die();
	}

}
