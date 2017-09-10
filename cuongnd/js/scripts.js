jQuery(function ($) {

    'use strict';

    // --------------------------------------------------------------------
    // PreLoader
    // --------------------------------------------------------------------

    (function () {
        $('#preloader').delay(200).fadeOut('slow');
    }());
    (function () {
        new WOW().init();
    }());
    (function () {
        $("a.youtube").YouTubePopup({ autoplay: 0, draggable: true });
    }());
    (function () {
        $('html').smoothScroll(500);
    }());


    // --------------------------------------------------------------------
    // Owl Carousal
    // --------------------------------------------------------------------

    (function () {
        $("#review").owlCarousel({
            autoPlay: 3000, //Set AutoPlay to 3 seconds
            items: 2,
            itemsDesktop: [1199, 3],
            itemsDesktopSmall: [979, 3]

        });
    }());



}); // JQuery end