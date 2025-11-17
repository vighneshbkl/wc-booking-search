<?php
class HB_REST_API {
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    public function register_routes() {
        register_rest_route('hb/v1', '/hotels', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_hotels'),
            'permission_callback' => '__return_true',
        ));
    }

    public function get_hotels($request) {
        $location = $request->get_param('location');
        $check_in = $request->get_param('check_in');
        $check_out = $request->get_param('check_out');
        $adults = $request->get_param('adults');
        $children = $request->get_param('children');

        // Query WooCommerce products based on the parameters
        $args = array(
            'post_type' => 'product',
            'meta_query' => array(
                array(
                    'key' => 'hotel_location',
                    'value' => $location,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $query = new WP_Query($args);
        $hotels = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $hotels[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'permalink' => get_permalink(),
                    'price' => get_post_meta(get_the_ID(), '_price', true),
                );
            }
            wp_reset_postdata();
        }

        return new WP_REST_Response($hotels, 200);
    }
}
?>