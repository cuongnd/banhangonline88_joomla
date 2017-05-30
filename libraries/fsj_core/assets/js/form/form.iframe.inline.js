
// inline mode - only on edit screen. list in inline mode is within a separate iframe
jQuery(document).ready(function () {

    Joomla.submitbutton = function (task) {
        task_bare = task;

        if (task_bare.indexOf('.') > -1) {
            task_bare = task_bare.split(".")[1];
        }
        if (task_bare == "cancel") {
            // hide this iframe now!
            parent.fsj_modal_hide();
            return;
        }
        Joomla.submitform(task, jQuery("#item-form")[0]);
    }

});
