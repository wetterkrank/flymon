<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Flymon
 * Plugin URI:        http://escapefromberl.in/flight-prices-monitor/
 * Description:       Displays the lowest flight price for selected route & time range
 * Version:           0.1
 * Author:            wetterkrank
 * Author URI:        http://escapefromberl.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       flymon
 * Domain Path:       /languages
 */

// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'FPM_VERSION', '0.1' );

// The code that runs during plugin activation
function activate_flight_prices_monitor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fpm-activator.php';
	Flight_Prices_Monitor_Activator::activate();
}

// The code that runs during plugin deactivation
function deactivate_flight_prices_monitor() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fpm-deactivator.php';
	Flight_Prices_Monitor_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_flight_prices_monitor' );
register_deactivation_hook( __FILE__, 'deactivate_flight_prices_monitor' );

// The core plugin class; defines i18n, admin-specific hooks, and public-facing site hooks
require plugin_dir_path( __FILE__ ) . 'includes/class-fpm.php';


// Begins execution of the plugin
function run_flight_prices_monitor() {
	$plugin = new Flight_Prices_Monitor();
	$plugin->run();
}
run_flight_prices_monitor();
