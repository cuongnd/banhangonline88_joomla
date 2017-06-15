// A $( document ).ready() block.
jQuery( document ).ready(function($) {
    $(window).on('ajaxComplete', function() {
        setTimeout(function() {
            $(window).lazyLoadXT();
        }, 50);
    });
    var y = $(window).scrollTop();  //your current y position on the page
    $(window).scrollTop(y+1);
});