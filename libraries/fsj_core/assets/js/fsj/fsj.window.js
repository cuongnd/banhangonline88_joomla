
jQuery(document).ready(function () {

    jQuery('button.close').click(function (ev) {
        ev.preventDefault();
        window.close();
    });

    jQuery('a.close_popup').click(function (ev) {
        ev.preventDefault();
        window.close();
    });

    resize();

    jQuery(window).resize(function () {
        resize();
    });
});

function resize() {
    var head_height = jQuery('.modal-header').outerHeight(true);
    var foot_height = jQuery('.modal-footer').outerHeight(true);

    var win_height = jQuery(window).height();

    var res_height = win_height - foot_height - head_height;

    jQuery('.modal-body').outerHeight(res_height);
}