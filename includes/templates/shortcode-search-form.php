<?php
// This file contains the HTML structure for the hotel search form.

?>

<form id="hotel-search-form" action="<?php echo esc_url( home_url( '/search-results' ) ); ?>" method="GET">
    <div class="form-group">
        <label for="location">Location</label>
        <input type="text" id="location" name="location" required>
    </div>
    <div class="form-group">
        <label for="check-in">Check-in Date</label>
        <input type="date" id="check-in" name="check_in" required>
    </div>
    <div class="form-group">
        <label for="check-out">Check-out Date</label>
        <input type="date" id="check-out" name="check_out" required>
    </div>
    <div class="form-group">
        <label for="rooms">Rooms</label>
        <input type="number" id="rooms" name="rooms" min="1" value="1" required>
    </div>
    <div class="form-group">
        <label for="adults">Adults</label>
        <input type="number" id="adults" name="adults" min="1" value="1" required>
    </div>
    <div class="form-group">
        <label for="children">Children</label>
        <input type="number" id="children" name="children" min="0" value="0">
    </div>
    <button type="submit">Search</button>
</form>