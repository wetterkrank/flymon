<?php

// Defines the internationalization functionality
class Flight_Prices_Monitor_i18n {

	// Loads the plugin text domain for translation
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'flight-prices-monitor',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
