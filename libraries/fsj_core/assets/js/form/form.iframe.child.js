jQuery(document).ready(function () {
    jQuery('.parent_popup').click(function (ev) {
        ev.preventDefault();
        window.parent.fsj_frame_popup(jQuery(this).attr('href'), window);
    });

    jQuery('#toolbar-new a').unbind('click');
    jQuery('#toolbar-new a').click(function (ev) {
        ev.preventDefault();
        return false;
    });

    try {
        if (typeof (Joomla) != "undefined")
        {
            Joomla.submitbutton = function (task) {
                if (task.split('.')[1] == "add") {
                    var qs = fsj_utils.parseQuerystring();
                    var url = window.location.pathname + '?option=' + qs['option'] + '&task=' + task;
                    window.parent.fsj_frame_popup(url, window);
                    return;
                } else if (task.split('.')[1] == "edit") {
                    var qs = fsj_utils.parseQuerystring();
                    var url = window.location.pathname + '?option=' + qs['option'] + '&task=' + task + '&id=' + qs['id'];
                    window.parent.fsj_frame_popup(url, window);
                    return;
                }
                Joomla.submitform(task);
            }
        }
    } catch (e) { }

    try {
        window.parent.fsj_frame_loaded(this, window);
    } catch (e) { }

    jQuery('#adminForm').on('submit', function () {
        try {
            window.parent.fsj_frame_loading(window);
        } catch (e) { }

        return true;
    });
});

/*
jQuery(window).unload(function () {
    window.parent.fsj_frame_loading(window);
    return true;
});
*/
jQuery(window).bind('beforeunload', function () {
    window.parent.fsj_frame_loading(window);
});
