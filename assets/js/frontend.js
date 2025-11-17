// This file contains JavaScript code for date persistence, form validation, auto-filling booking fields, and handling redirects.

document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.querySelector('#hotel-search-form');
    const checkInInput = document.querySelector('#check-in');
    const checkOutInput = document.querySelector('#check-out');

    // Load previously saved dates from localStorage
    if (localStorage.getItem('checkInDate')) {
        checkInInput.value = localStorage.getItem('checkInDate');
    }
    if (localStorage.getItem('checkOutDate')) {
        checkOutInput.value = localStorage.getItem('checkOutDate');
    }

    // Save dates to localStorage on change
    checkInInput.addEventListener('change', function() {
        localStorage.setItem('checkInDate', checkInInput.value);
    });

    checkOutInput.addEventListener('change', function() {
        localStorage.setItem('checkOutDate', checkOutInput.value);
    });

    // Form validation
    searchForm.addEventListener('submit', function(event) {
        const checkInDate = new Date(checkInInput.value);
        const checkOutDate = new Date(checkOutInput.value);

        if (checkInDate >= checkOutDate) {
            event.preventDefault();
            alert('Check-out date must be after check-in date.');
        }
    });

    // Auto-fill booking fields if URL parameters are present
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('check_in') && urlParams.has('check_out')) {
        checkInInput.value = urlParams.get('check_in');
        checkOutInput.value = urlParams.get('check_out');
    }
});