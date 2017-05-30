jQuery(document).ready(function () {
    jQuery('#attach_files tbody').sortable();
    jQuery('#attach_files').disableSelection();
})
function fsj_attach_remove(fileid)
{
    jQuery('#files_delete').val(jQuery('#files_delete').val() + "|" + fileid + "|");
    jQuery('#fsj_attach_' + fileid).remove();
}

function fsj_attach_presave(task)
{
    task = task.split('.')[1];
    if (task != "save" && task != "apply" && task != 'save2new' && task != 'save2copy')
        return true;

    // save ordering here!
    fsj_attach_update_order();

    // if there are any pending upload, force a wait!
    var any_up = false;

    jQuery('tr.template-upload').each(function () {
        if (jQuery(this).find('.error').text()) {
            // not needed as we have an error!
        } else {
            any_up = true;
        }
    });

    if (any_up) {
        alert("Please wait until all files have finished uploaded before saving this item");
        return false;
    }

    return true;
}

function fsj_attach_update_order()
{
    var order = 1;
    jQuery('#attach_files').find('.order').each(function () {
        jQuery(this).val(order);
        order++;
    });
}