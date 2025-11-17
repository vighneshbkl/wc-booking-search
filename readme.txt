=== Hotel Booking Plugin ===
Contributors: yourname
Tags: hotel, booking, woocommerce, travel, accommodation
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==
The Hotel Booking Plugin allows users to search for hotels and make bookings directly through your WooCommerce store. It integrates seamlessly with WooCommerce Bookings, providing a user-friendly search form, results page, and single hotel page modifications.

== Installation ==
1. Upload the entire `hotel-booking-plugin` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Use the `[hotel_search_form]` shortcode to display the search form on any page or post.

== Usage ==
After installation, you can add the search form to any page using the shortcode `[hotel_search_form]`. Users can input their desired location, check-in and check-out dates, and the number of guests. The results will be displayed on the same page or a designated results page.

== Frequently Asked Questions ==
= How do I customize the search form? =
You can modify the search form template located in `includes/templates/shortcode-search-form.php`.

= Can I translate the plugin? =
Yes, the plugin is translation-ready. You can use the provided `.pot` file in the `languages` folder to create your translations.

== Changelog ==
= 1.0 =
* Initial release of the Hotel Booking Plugin.