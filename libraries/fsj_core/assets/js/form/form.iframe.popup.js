// must be used and included when a modal window with and iframe is used. This will
// resize the iframe and adjust the styles as required

jQuery(document).ready(function () {
    // add classes to body to make things play nice
    jQuery('body').addClass('body-modal');
    jQuery('body').addClass('fsj');
    jQuery('body').removeClass('modal');

    // hide system message container
    if (jQuery('#system-message').children().length < 1)
        jQuery('#system-message-container').hide();

    // move system message to modal body as some tempaltes it makes a mess
    jQuery('#system-message-container').prependTo(jQuery('.modal-body'));

    jQuery('button.close').click(function (ev) {
        ev.preventDefault();
        parent.fsj_modal_hide();
    });

    jQuery('a.close_popup').click(function (ev) {
        ev.preventDefault();
        parent.fsj_modal_hide();
    });

    fsj_resize_popup();

    setInterval("fsj_resize_popup()", 500);
});

var fsj_resize_height = 0;
function fsj_resize_popup() {
    var window_height = jQuery(parent.window).height();

    // fix for some stubborn templates with firefix
    if (window_height < parent.window.innerHeight)
        window_height = parent.window.innerHeight;

    jQuery('div.modal-body').css('max-height', window_height - 400 + 'px');

    var sheight = document.body.scrollHeight;

    if (fsj_resize_height != sheight) {
        fsj_resize_height = sheight;
        var offset = 200;
        if (sheight + offset > window_height) {
            sheight = window_height - offset;
        } else {
            parent.jQuery('#fsj_modal iframe').attr("scrolling", "no");
        }

        if (sheight < 50)
            sheight = 300;

        parent.jQuery('#fsj_modal iframe').removeAttr('style');
        parent.jQuery('#fsj_modal iframe').css('height', sheight + 'px');
    }
}
