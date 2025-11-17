<?php
/**
 * Plugin Name: Hotel Booking Plugin
 * Description: A plugin to implement hotel booking functionality using WooCommerce Bookings.
 * Version: 1.0
 * Author: Your Name
 * Text Domain: hotel-booking-plugin
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'HB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include necessary classes
require_once HB_PLUGIN_DIR . 'includes/class-hb-plugin.php';
require_once HB_PLUGIN_DIR . 'includes/class-hb-woo-integration.php';
require_once HB_PLUGIN_DIR . 'includes/class-hb-shortcodes.php';
require_once HB_PLUGIN_DIR . 'includes/class-hb-rest-api.php';

// Initialize the plugin
function hb_init() {
    $hb_plugin = new HB_Plugin();
    $hb_plugin->init();
}
add_action( 'plugins_loaded', 'hb_init' );

// Enqueue assets
function hbs_enqueue_assets() {
    // Ensure base URL is plugin root
    $base = plugin_dir_url( __FILE__ );

    // flatpickr from CDN
    wp_enqueue_style( 'flatpickr-css', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css', array(), '4.6.13' );
    wp_enqueue_style( 'hbs-frontend-css', $base . 'assets/css/frontend.css', array(), '1.0' );

    wp_enqueue_script( 'flatpickr-js', 'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js', array(), '4.6.13', true );
    wp_enqueue_script( 'hbs-frontend-js', $base . 'assets/js/frontend.js', array( 'flatpickr-js', 'jquery' ), '1.0', true );

    // Localize data for JS
    $data = array(
        'rest_url'        => esc_url_raw( rest_url( 'hotel-booking-search/v1/locations' ) ),
        'search_results_page' => esc_url_raw( home_url( '/hotel-search-results/' ) ),
        'today'           => date( 'Y-m-d' ),
        'ajax_nonce'      => wp_create_nonce( 'hbs_nonce' ),
        'defaults'        => array(
            'location' => isset( $_GET['location'] ) ? sanitize_text_field( wp_unslash( $_GET['location'] ) ) : '',
            'checkin'  => isset( $_GET['checkin'] ) ? sanitize_text_field( wp_unslash( $_GET['checkin'] ) ) : '',
            'checkout' => isset( $_GET['checkout'] ) ? sanitize_text_field( wp_unslash( $_GET['checkout'] ) ) : '',
        ),
    );
    wp_localize_script( 'hbs-frontend-js', 'HBS_DATA', $data );
}
add_action( 'wp_enqueue_scripts', 'hbs_enqueue_assets' );