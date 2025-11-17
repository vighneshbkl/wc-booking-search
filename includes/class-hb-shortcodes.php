<?php
class HB_Shortcodes {
    public function __construct() {
        add_shortcode('hotel_search_form', [$this, 'render_search_form']);
        add_shortcode('hotel_search_results', [$this, 'render_search_results']);
    }

    public function render_search_form() {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/shortcode-search-form.php';
        return ob_get_clean();
    }

    public function render_search_results($atts) {
        $atts = shortcode_atts([
            'location' => '',
            'check_in' => '',
            'check_out' => '',
            'adults' => 1,
            'children' => 0,
        ], $atts);

        // Query WooCommerce products based on the search criteria
        $args = [
            'post_type' => 'product',
            'meta_query' => [
                // Add your custom meta queries here based on the search criteria
            ],
        ];

        $query = new WP_Query($args);

        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/search-results.php';
        return ob_get_clean();
    }
}
?>