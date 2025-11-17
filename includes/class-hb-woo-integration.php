<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class HB_Woo_Integration {

    public function __construct() {
        add_action( 'init', array( $this, 'register_custom_post_types' ) );
        add_action( 'wp_ajax_hb_search_hotels', array( $this, 'ajax_search_hotels' ) );
        add_action( 'wp_ajax_nopriv_hb_search_hotels', array( $this, 'ajax_search_hotels' ) );
    }

    public function register_custom_post_types() {
        // Register custom post types for hotels if needed
    }

    public function ajax_search_hotels() {
        // Validate and sanitize input
        $location = sanitize_text_field( $_POST['location'] );
        $check_in = sanitize_text_field( $_POST['check_in'] );
        $check_out = sanitize_text_field( $_POST['check_out'] );
        $adults = intval( $_POST['adults'] );
        $children = intval( $_POST['children'] );

        // Query WooCommerce products based on the search criteria
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'hotel_location',
                    'value' => $location,
                    'compare' => 'LIKE',
                ),
                array(
                    'key' => 'booking_start_date',
                    'value' => $check_in,
                    'compare' => '>=',
                    'type' => 'DATE',
                ),
                array(
                    'key' => 'booking_end_date',
                    'value' => $check_out,
                    'compare' => '<=',
                    'type' => 'DATE',
                ),
            ),
        );

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            $results = array();
            while ( $query->have_posts() ) {
                $query->the_post();
                $results[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'price' => get_post_meta( get_the_ID(), '_price', true ),
                    'thumbnail' => get_the_post_thumbnail_url( get_the_ID(), 'medium' ),
                );
            }
            wp_send_json_success( $results );
        } else {
            wp_send_json_error( 'No hotels found for the given criteria.' );
        }

        wp_die();
    }
}
?>