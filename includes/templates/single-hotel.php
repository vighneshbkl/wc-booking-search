<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $post;

// Accept both param styles; URL params take precedence
$checkin  = isset( $_GET['checkin'] ) ? sanitize_text_field( wp_unslash( $_GET['checkin'] ) ) : ( isset( $_GET['check_in'] ) ? sanitize_text_field( wp_unslash( $_GET['check_in'] ) ) : '' );
$checkout = isset( $_GET['checkout'] ) ? sanitize_text_field( wp_unslash( $_GET['checkout'] ) ) : ( isset( $_GET['check_out'] ) ? sanitize_text_field( wp_unslash( $_GET['check_out'] ) ) : '' );

// Get the hotel booking product details
$hotel_title = get_the_title();
$hotel_description = get_the_content();
$hotel_price = get_post_meta( $post->ID, '_price', true );
$hotel_image = get_the_post_thumbnail_url( $post->ID, 'full' );

?>
<div class="single-hotel">
    <h1><?php echo esc_html( $hotel_title ); ?></h1>
    <div class="hotel-image">
        <img src="<?php echo esc_url( $hotel_image ); ?>" alt="<?php echo esc_attr( $hotel_title ); ?>">
    </div>
    <div class="hotel-description">
        <?php echo wp_kses_post( $hotel_description ); ?>
    </div>
    <div class="hotel-price">
        <p><?php echo esc_html( wc_price( $hotel_price ) ); ?></p>
    </div>
    
    <div class="booking-form">
        <h2>Book Your Stay</h2>
        <!-- Hidden fields to preserve search values if needed -->
        <form action="<?php echo esc_url( get_permalink() ); ?>" method="post" class="hbs-single-booking-form">
            <input type="hidden" name="checkin" value="<?php echo esc_attr( $checkin ); ?>">
            <input type="hidden" name="checkout" value="<?php echo esc_attr( $checkout ); ?>">
            <button type="submit" class="button single_add_to_cart_button">Book Now</button>
        </form>
    </div>
</div>

<script type="text/javascript">
(function(){
    'use strict';

    var productId = "<?php echo intval( $post->ID ); ?>";
    var sessionKey = 'hbs_auto_added_' + productId;
    var CART_URL = "<?php echo esc_js( wc_get_cart_url() ); ?>";
    var ADD_TO_CART_AJAX = "<?php echo esc_url_raw( admin_url( 'admin-ajax.php' ) ); ?>?wc-ajax=add_to_cart";
    var HOME_URL = "<?php echo esc_js( home_url( '/' ) ); ?>";

    function getParamOrStorage(name, altName) {
        var params = new URLSearchParams(window.location.search);
        var v = params.get(name) || (altName ? params.get(altName) : null);
        if (!v) v = localStorage.getItem('hbs_' + name) || localStorage.getItem('hbs_' + (altName || name));
        return v || '';
    }

    var ci = getParamOrStorage('checkin', 'check_in');
    var co = getParamOrStorage('checkout', 'check_out');

    if (!ci && !co) return;

    function setInputValue(selectors, value) {
        for (var i = 0; i < selectors.length; i++) {
            var el = document.querySelector(selectors[i]);
            if (el) {
                el.value = value;
                el.dispatchEvent(new Event('input', { bubbles: true }));
                el.dispatchEvent(new Event('change', { bubbles: true }));
                return true;
            }
        }
        return false;
    }

    function fetchAddToCart(ciVal, coVal) {
        var fd = new FormData();
        fd.append('product_id', productId);
        fd.append('quantity', 1);

        // include many possible booking parameter names
        var startNames = ['wc_bookings_field_start','booking_date_from','booking_date','booking_start_date','start_date','checkin'];
        var endNames   = ['wc_bookings_field_end','booking_date_to','booking_end_date','end_date','checkout'];

        startNames.forEach(function(n){ fd.append(n, ciVal); });
        endNames.forEach(function(n){ fd.append(n, coVal); });

        return fetch(ADD_TO_CART_AJAX, {
            method: 'POST',
            credentials: 'same-origin',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function(r){
            return r.text().then(function(text){
                // try parse JSON, else return raw text
                try { return JSON.parse(text); } catch(e) { return { raw: text }; }
            });
        }).catch(function(err){ return { error: String(err) }; });
    }

    function submitFormToSite(ciVal, coVal) {
        // create form GET to home URL with add-to-cart param and booking fields
        var f = document.createElement('form');
        f.method = 'GET';
        f.action = HOME_URL;
        f.style.display = 'none';

        function add(name, value){
            var i = document.createElement('input');
            i.type = 'hidden';
            i.name = name;
            i.value = value;
            f.appendChild(i);
        }

        add('add-to-cart', productId);
        add('quantity', '1');

        // include booking fields that many handlers accept
        var startNames = ['wc_bookings_field_start','booking_date_from','booking_date','booking_start_date','start_date','checkin'];
        var endNames   = ['wc_bookings_field_end','booking_date_to','booking_end_date','end_date','checkout'];

        startNames.forEach(function(n){ add(n, ciVal); });
        endNames.forEach(function(n){ add(n, coVal); });

        document.body.appendChild(f);
        f.submit();
    }

    function redirectWithGetAdd(ciVal, coVal) {
        // fallback: use GET add-to-cart on home URL
        try {
            var u = new URL(HOME_URL, window.location.origin);
            u.searchParams.set('add-to-cart', productId);
            u.searchParams.set('quantity', '1');
            if (ciVal) u.searchParams.set('checkin', ciVal);
            if (coVal) u.searchParams.set('checkout', coVal);
            window.location.href = u.toString();
        } catch (e) {
            window.location.href = HOME_URL;
        }
    }

    document.addEventListener('DOMContentLoaded', function(){
        var startSelectors = [
            'input.booking_date_from',
            'input[name="booking_date_from"]',
            'input[name="start_date"]',
            'input[name="wc_bookings_field_start"]',
            'input[name="bookings_start_date"]',
            'input[data-booking-start]'
        ];
        var endSelectors = [
            'input.booking_date_to',
            'input[name="booking_date_to"]',
            'input[name="end_date"]',
            'input[name="wc_bookings_field_end"]',
            'input[name="bookings_end_date"]',
            'input[data-booking-end]'
        ];

        setInputValue(startSelectors, ci);
        setInputValue(endSelectors, co);

        try { if (sessionStorage.getItem(sessionKey)) return; } catch(e){}

        // 1) Try wc-ajax add_to_cart via fetch
        fetchAddToCart(ci, co).then(function(resp){
            console.log('hbs: add_to_cart response', resp);
            var ok = false;
            if (resp && (resp.fragments || resp.success || resp.added)) ok = true;
            // some themes return raw html/text - attempt to detect common indicators
            if (!ok && resp && resp.raw && (String(resp.raw).indexOf('cart') !== -1 || String(resp.raw).indexOf('added') !== -1)) ok = true;

            if (ok) {
                try { sessionStorage.setItem(sessionKey, '1'); } catch(e){}
                window.location.href = CART_URL;
                return;
            }

            // 2) If not ok, try to submit site form (if present)
            var cartForm = document.querySelector('form.cart') || document.querySelector('form.hbs-single-booking-form');
            if (cartForm) {
                // ensure booking scripts react, then click submit
                setTimeout(function(){
                    try { sessionStorage.setItem(sessionKey, '1'); } catch(e){}
                    var btn = cartForm.querySelector('button[type="submit"], input[type="submit"], .single_add_to_cart_button');
                    if (btn && typeof btn.click === 'function') { btn.click(); }
                    else { try { cartForm.submit(); } catch(err){} }
                    // redirect to cart after short delay
                    setTimeout(function(){ window.location.href = CART_URL; }, 1400);
                }, 900);
                return;
            }

            // 3) Try GET add-to-cart on home (common fallback)
            try { submitFormToSite(ci, co); } catch(e) { redirectWithGetAdd(ci, co); }
        }).catch(function(err){
            console.error('hbs: add_to_cart fetch failed', err);
            // fallback chain
            var cartForm = document.querySelector('form.cart') || document.querySelector('form.hbs-single-booking-form');
            if (cartForm) {
                try { sessionStorage.setItem(sessionKey, '1'); } catch(e){}
                var btn = cartForm.querySelector('button[type="submit"], input[type="submit"], .single_add_to_cart_button');
                if (btn && typeof btn.click === 'function') { btn.click(); }
                else { try { cartForm.submit(); } catch(err){} }
                setTimeout(function(){ window.location.href = CART_URL; }, 1400);
                return;
            }
            try { submitFormToSite(ci, co); } catch(e) { redirectWithGetAdd(ci, co); }
        });
    });
})();
</script>