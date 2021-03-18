<?php

class KIWI_API {
	const CURRENCIES = ['AED','AFN','ALL','AMD','ANG','AOA','ARS','AUD','AWG','AZN','BAM','BBD','BDT','BGN','BHD','BIF','BMD','BND','BOB','BRL','BSD','BTC','BTN','BWP','BYR','BZD','CAD','CDF','CHF','CLF','CLP','CNY','COP','CRC','CUC','CUP','CVE','CZK','DJF','DKK','DOP','DZD','EEK','EGP','ERN','ETB','EUR','FJD','FKP','GBP','GEL','GGP','GHS','GIP','GMD','GNF','GTQ','GYD','HKD','HNL','HRK','HTG','HUF','IDR','ILS','IMP','INR','IQD','IRR','ISK','JEP','JMD','JOD','JPY','KES','KGS','KHR','KMF','KPW','KRW','KWD','KYD','KZT','LAK','LBP','LKR','LRD','LSL','LTL','LVL','LYD','MAD','MDL','MGA','MKD','MMK','MNT','MOP','MRO','MTL','MUR','MVR','MWK','MXN','MYR','MZN','NAD','NGN','NIO','NOK','NPR','NZD','OMR','PAB','PEN','PGK','PHP','PKR','PLN','PYG','QAR','QUN','RON','RSD','RUB','RWF','SAR','SBD','SCR','SDG','SEK','SGD','SHP','SLL','SOS','SRD','STD','SVC','SYP','SZL','THB','TJS','TMT','TND','TOP','TRY','TTD','TWD','TZS','UAH','UGX','USD','UYU','UZS','VEF','VND','VUV','WST','XAF','XAG','XAU','XCD','XDR','XOF','XPD','XPF','XPT','YER','ZAR','ZMK','ZMW','ZWL'];
	const LOCALES = ['ae','ag','ar','at','au','be','bg','bh','br','by','ca','ca-fr','ch','cl','cn','co','ct','cz','da','de','dk','ec','ee','el','en','es','fi','fr','gb','gr','hk','hr','hu','id','ie','il','in','is','it','ja','jo','jp','ko','kr','kw','kz','lt','mx','my','nl','no','nz','om','pe','ph','pl','pt','qa','ro','rs','ru','sa','se','sg','sk','sr','sv','th','tr','tw','ua','uk','us','vn','za'];

	private $config;

	public function __construct( $config ) {
		$this->config = $config;
	}

	// Gets the price from Kiwi.com
	function request_price($params, $config) {
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
			$output['deeplink'] = $first_result['deep_link'];
		}
		return $output;
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