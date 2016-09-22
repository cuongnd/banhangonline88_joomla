jQuery(document).ready(function () {

    // fix the image select modal input. The bootstrap styles do odd things to it
    jQuery('.fsj a.modal').addClass('hide');
    setTimeout("fsj_sort_modal_links()", 500);

    fsj_init_shortcut_keys();
});

function fsj_sort_modal_links() {
    jQuery('.fsj a.modal').each(function () {
        jQuery(this).removeClass('modal');
        jQuery(this).removeClass('hide');
    });
}

var fsj_shortcut_keys = [];
function fsj_init_shortcut_keys()
{
    jQuery(document).keydown(function (e) {

        //console.log(e);
        //console.log(fsj_shortcut_keys);

        for (var i in fsj_shortcut_keys)
        {
            shortcut = fsj_shortcut_keys[i];

            if (e.ctrlKey == shortcut.isCtrl && e.shiftKey == shortcut.isShift && e.altKey == shortcut.isAlt && e.keyCode == shortcut.keyCode.charCodeAt(0))
            {
                //alert(shortcut.action);
                if (shortcut.action)
                {
                    Joomla.submitbutton(shortcut.action);
                } else if (shortcut.code)
                {
                    eval(shortcut.code);
                } else {
                    jQuery(shortcut.element + " > button").trigger("click");
                }
                return false;
            } else {
                /*console.log("Check " + i);
                console.log(e.ctrlKey + " == " + shortcut.isCtrl + " => " + (e.ctrlKey == shortcut.isCtrl));
                console.log(e.shiftKey + " == " + shortcut.isShift + " => " + (e.shiftKey == shortcut.isShift));
                console.log(e.altKey + " == " + shortcut.isAlt + " => " + (e.altKey == shortcut.isAlt));
                console.log(e.keyCode + " == " + shortcut.keyCode.charCodeAt(0) + " => " + (e.keyCode == shortcut.keyCode.charCodeAt(0)));*/
            }
        }
    });
}

function fsj_add_shortcut_key(isCtrl, isShift, isAlt, keyCode, element, action, code)
{
    if (jQuery(element).length < 1) return;

    shortcut = { isCtrl: isCtrl, isShift: isShift, isAlt: isAlt, keyCode: keyCode, element: element, action: action, code: code };

    fsj_shortcut_keys.push(shortcut);

    var name = keyCode;

    if (isShift) name = "Shift + " + name;
    if (isAlt) name = "Alt + " + name;
    if (isCtrl) name = "Ctrl + " + name;

    var title = jQuery(element).attr('title');

    if (title) {
        name = title + " &nbsp;&nbsp; / &nbsp;&nbsp; " + name;
    }
    jQuery(element).attr('title', name);
    jQuery(element).addClass('tt-delay');
}
