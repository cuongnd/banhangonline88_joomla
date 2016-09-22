
var fsj_frame_current = null;

function fsj_frame_popup(url, frame) {
    fsj_frame_current = frame;
    var frameid = jQuery(frame.frameElement).attr('id');
    fsj_modal_show(url, true, 800, "fsj_reload_frame('" + frameid + "')");
    //TINY.box.show({ iframe: url, width: 800, height: '90%', scrolling: 1, animate: false })
}
function fsj_frame_closed(url) {
    TINY.box.hide();
    jQuery(fsj_frame_current).attr('src', url);
    fsj_frame_current.location.reload();
}

function fsj_frame_show_in_tab(url, iframe, tab) {
    /*console.log("fsj_frame_show_in_tab");
    console.log(iframe);
    console.log(tab);
    console.log(url);
    console.log(window.frames[iframe].location);
    window.frames[iframe].location = url;
    console.log(window.frames[iframe].location);*/
    window.open(url, iframe);

    jQuery('a[href="'+tab+'"]').tab('show');
}

jQuery(document).ready(function () {
    fsj_frame_load_frames();
});

function fsj_frame_load_frames() {
    var frames = jQuery('iframe.fsj_iframe_dl');
    if (frames.length < 1)
        return;
    var frame = frames[0];

    jQuery(frame).iframeAutoHeight({ heightOffset: 95, debug: true });

    jQuery(frame).load(function () {
        //fsj_iframe_height(this);
        fsj_frame_load_frames();
    });

    jQuery(frame).attr('src', jQuery(frame).attr('data-src'));
    jQuery(frame).removeClass('fsj_iframe_dl');
}

function fsj_iframe_height(iframe) {
    var main_div = jQuery(iframe.contentDocument).find('div.fsj');
    if (main_div.length > 0) {
        if (jQuery(main_div).parent().outerHeight() > 0)
            jQuery(iframe).css('height', jQuery(main_div).parent().outerHeight() + 100 + 'px');
    } else {
        if (iframe.contentDocument.body.offsetHeight > 0)
            jQuery(iframe).css('height', iframe.contentDocument.body.offsetHeight + 100 + 'px');
    }
}

function fsj_frame_loaded(frame, parent) {
    var id = "";
    if (typeof (frame.window) != "undefined") {
        id = jQuery(frame.window.frameElement).attr('id');
    } else if (typeof (parent) != "undefined") {
        id = jQuery(parent.frameElement).attr('id');
    }
    jQuery('#' + id + '_wait').hide();
}

function fsj_frame_loading(frame) {
    var id = jQuery(frame.frameElement).attr('id');
    jQuery('#' + id + '_wait').show();
}

function fsj_reload_frame(frameid) {
    var frame = jQuery('#' + frameid);
    var url = frame.attr('src');
    //frame.attr('src', '');
    frame.attr('src', url);
}