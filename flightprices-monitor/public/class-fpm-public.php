<?php

class Flight_Prices_Monitor_Public {

	private $name;
	private $version;

	// Configuration array, received on init
	private $config;

	// Initializes the class and sets its properties
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
			'const FLYMON = ' . json_encode( array(	'ajaxUrl' => admin_url('admin-ajax.php') ) ), 
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
		), $atts);

		// Validate the input and set the rest of the defaults
		$a['from'] = filter_var($a['from'], FILTER_SANITIZE_STRING);
		$a['to'] = filter_var($a['to'], FILTER_SANITIZE_STRING);

		if ( ($earliest = strtotime($a['earliest'])) === false ) {
			$a['earliest'] = date('d/m/Y');
		} else {
			$a['earliest'] = date('d/m/Y', $earliest);
		}

		if ( ($latest = strtotime($a['latest'])) === false ) {
			$a['latest'] = date('d/m/Y', strtotime("+3 months"));
		} else {
			$a['latest'] = date('d/m/Y', $latest);
		}

		$a['min_days'] = filter_var($a['min_days'], FILTER_SANITIZE_NUMBER_INT);
		$a['max_days'] = filter_var($a['max_days'], FILTER_SANITIZE_NUMBER_INT);            

		if ( !in_array($a['currency'], ['AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BRL','BSD','BTC','BTN','BWP','BYR','BZD','CAD','CDF','CHF','CLF','CLP','CNY','COP','CRC','CUC','CUP','CVE','CZK','DJF','DKK','DOP','DZD','EEK','EGP','ERN','ETB','EUR','FJD','FKP','GBP','GEL','GGP','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','IMP','INR','IQD','IRR','ISK','JEP','JMD','JOD','JPY','KES','KGS','KHR','KMF','KPW','KRW','KWD','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','LVL','LYD','MAD','MDL','MGA','MKD','MMK','MNT','MOP','MRO','MTL','MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','OMR','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','QUN','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SEK','SGD','SHP','SLL','SOS','SRD','STD','SVC','SYP','SZL','THB','TJS','TMT','TND','TOP','TRY','TTD','TWD','TZS','UAH','UGX','USD','UYU','UZS','VEF','VND','VUV','WST','XAF','XAG','XAU','XCD','XDR','XOF','XPD','XPF','XPT','YER','ZAR','ZMK','ZMW','ZWL']) ) {
			$a['currency'] = 'EUR';
		}

		if ( !in_array($a['locale'], ['ae','ag','ar','at','au','be','bg','bh','br','by','ca','ca-fr','ch','cl','cn','co','ct','cz','da','de','dk','ec','ee','el','en','es','fi','fr','gb','gr','hk','hr','hu','id','ie','il','in','is','it','ja','jo','jp','ko','kr','kw','kz','lt','mx','my','nl','no','nz','om','pe','ph','pl','pt','qa','ro','rs','ru','sa','se','sg','sk','sr','sv','th','tr','tw','ua','uk','us','vn','za']) ) {
			$a['locale'] = 'en';
		}

		$a['transport'] = str_replace(' ', '', $a['transport']);
		$vehicles = explode(',', $a['transport']);
		$unlisted = array_diff($vehicles, ['aircraft', 'bus', 'train']);
		if (count($unlisted) !== 0)
			$a['transport'] = 'aircraft';

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
				. '"';
		// Mind the ellipsis dots in one line
		return '<span class="flymon-tag" ' . $data . '>
			<span class="flymon-tag__dot1">.</span><span class="flymon-tag__dot2">.</span><span class="flymon-tag__dot3">.</span>
		</span>';
	}

	// Adds AJAX handlers for the public-facing side of the site.
	public function add_ajax_handlers() {
		add_action( 'wp_ajax_nopriv_price', array ($this, 'get_price') );
		add_action( 'wp_ajax_price', array ($this, 'get_price') );
	}

	// Handler for the AJAX price request; returns the price JSON
	function get_price() {
		// TODO: auth: check_ajax_referer(), wp_nonce() (?)
		$query = $_POST;
		$response = [];
		
		// TODO: validate the rest of the query params
		unset($query['action']);
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
			$price = $this->request_from_kiwi($query, $config);
			if ( !$price ) $this->throw_error(500, 'Could not retrieve the price from Kiwi, unknown error');
			
			// TODO: sanitize for SQL
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

	// Gets the price from Kiwi.com
	function request_from_kiwi($params, $config) {
		$url_base = 'https://tequila-api.kiwi.com/v2/search';
		$extras = [
			'partner_market' => $config['partner_market'], // NOTE: move to shortcode?
			'sort' => 'price',
			'asc' => 1,
			'limit' => 1
		];
		$query = array_merge($params, $extras);
		$url = $url_base . '?' . http_build_query($query);
		$args = array(
			'headers' => array('apikey' => $config['apikey']),
			'timeout' => 45
		);
		$response = wp_remote_get($url, $args);
		
		if ( is_array($response) 
			&& !is_wp_error($response) 
			&& wp_remote_retrieve_response_code($response) === 200 ) 
		{
			$output = $this->package_output($response);
			if ( ! $output ) return false;
			$output['deeplink'] = $this->make_search_deeplink($params, $config);
			return $output;
		} else {
			error_log('API Request Error: ' . print_r($response, true));
			return false; // or new WP_Error('code', 'message');
		}
	}

	// Converts the Kiwi API response into price-route-deeplink object
	function package_output(array $api_response) {
		$output = [];
		$body = json_decode( $api_response['body'], true ); // 'true' to return an array
		if ($body['data']) { // no data when nothing found
			$first_result = $body['data'][0];
			$output['price'] = $first_result['price'];
			$output['currency'] = $body['currency'];
			$output['from'] = $first_result['flyFrom'];
			$output['to'] = $first_result['flyTo'];
			$output['outboundDate'] = date("d-m-Y", strtotime($first_result['utc_departure']));
			$returnLeg = $this->array_where( $first_result['route'], fn($leg) => $leg['return'] === 1 );
			$output['inboundDate'] = date("d-m-Y", strtotime($returnLeg['utc_departure']));
			$output['booking_deeplink'] = $first_result['deep_link'];
			$output['lastChecked'] = time(); // Unix timestamp
		}
		return $output;
	}

	// Assembles the landing page link using the original request parameters and API call config
	function make_search_deeplink(array $params, array $config) {
		$deeplink_host = 'https://www.kiwi.com/deep';
		$deeplink_params = [
			'from' => $params['fly_from'],
			'to' => $params['fly_to'],
			'departure' => str_replace('/', '-', $params['date_from'].'_'.$params['date_to']),
			'return' => $params['nights_in_dst_from'] . '-' . $params['nights_in_dst_to'],
			'lang' => $params['locale'],
			'currency' => $params['curr'],
			'transport' => $params['vehicle_type'],
			'stopNumber' => $params['max_stopovers'],
			'affilid' => $config['affilid'],
		];
		$deeplink = $deeplink_host . '?' . http_build_query($deeplink_params);
		return $deeplink;
	}

	// Applies $fn to each value and returns the 1st value where $fn is true
	function array_where(array $arr, callable $fn) {
		foreach ($arr as $x) {
			if (call_user_func($fn, $x) === true)
				return $x;
		}
		return null;
	}
	  
}
