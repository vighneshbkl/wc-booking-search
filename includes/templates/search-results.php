<?php
// This file displays the search results for hotel bookings based on user input.

get_header();

$location = isset($_GET['location']) ? sanitize_text_field($_GET['location']) : '';
$check_in = isset($_GET['check_in']) ? sanitize_text_field($_GET['check_in']) : '';
$check_out = isset($_GET['check_out']) ? sanitize_text_field($_GET['check_out']) : '';
$rooms = isset($_GET['rooms']) ? intval($_GET['rooms']) : 1;
$adults = isset($_GET['adults']) ? intval($_GET['adults']) : 1;
$children = isset($_GET['children']) ? intval($_GET['children']) : 0;

// Query WooCommerce products based on the search criteria
$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'location', // Assuming 'location' is a custom field for the hotel
            'value' => $location,
            'compare' => 'LIKE',
        ),
        // Additional meta queries can be added here for check-in/check-out dates if needed
    ),
);

$query = new WP_Query($args);

if ($query->have_posts()) : ?>
    <div class="hotel-search-results">
        <h2><?php echo esc_html__('Search Results for:', 'hotel-booking-plugin'); ?> <?php echo esc_html($location); ?></h2>
        <div class="results-list">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="hotel-card">
                    <h3><?php the_title(); ?></h3>
                    <div class="hotel-thumbnail">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                    <div class="hotel-details">
                        <p><?php echo esc_html__('Location:', 'hotel-booking-plugin') . ' ' . esc_html($location); ?></p>
                        <p><?php echo esc_html__('Check-in:', 'hotel-booking-plugin') . ' ' . esc_html($check_in); ?></p>
                        <p><?php echo esc_html__('Check-out:', 'hotel-booking-plugin') . ' ' . esc_html($check_out); ?></p>
                        <p><?php echo esc_html__('Rooms:', 'hotel-booking-plugin') . ' ' . esc_html($rooms); ?></p>
                        <p><?php echo esc_html__('Adults:', 'hotel-booking-plugin') . ' ' . esc_html($adults); ?></p>
                        <p><?php echo esc_html__('Children:', 'hotel-booking-plugin') . ' ' . esc_html($children); ?></p>
                        <a href="<?php the_permalink(); ?>" class="button"><?php echo esc_html__('View Details', 'hotel-booking-plugin'); ?></a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
<?php else : ?>
    <p><?php echo esc_html__('No hotels found for your search criteria.', 'hotel-booking-plugin'); ?></p>
<?php endif;

wp_reset_postdata();

get_footer();
?>