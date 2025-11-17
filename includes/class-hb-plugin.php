<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HB_Plugin {
    public function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        $this->register_shortcodes();
    }

    public function init() {
        // Register custom post types, taxonomies, etc. here
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'hb-frontend-style', plugins_url( 'assets/css/frontend.css', __FILE__ ) );
        wp_enqueue_script( 'hb-frontend-script', plugins_url( 'assets/js/frontend.js', __FILE__ ), array( 'jquery' ), null, true );
    }

    private function register_shortcodes() {
        // Include the shortcodes class
        require_once plugin_dir_path( __FILE__ ) . 'class-hb-shortcodes.php';
        $shortcodes = new HB_Shortcodes();
    }
}
?>