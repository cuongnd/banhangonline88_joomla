jQuery(document).ready(function () {

    // when checkbox of component changes, update the value field

    jQuery('.fsj_checklist_checkbox').click(function (ev) {
        var input = jQuery(this).attr('field');

        var thisvalue = jQuery(this).val();

        var mainvalue = jQuery('#' + input).val();

        if (mainvalue.length > 0) {
            var values = mainvalue.split(";");
        } else {
            var values = [];
        }

        if (jQuery(this).attr('checked')) {
            values.push(thisvalue);
        } else {
            for (var i = values.length - 1; i >= 0; i--) {
                if (values[i] === thisvalue) {
                    values.splice(i, 1);
                }
            }
        }

        jQuery('#' + input).val(values.join(";"));
    });

    jQuery('.fsj_checklist_checkall').click(function (ev) {
        ev.preventDefault();

        var input = jQuery(this).attr('field');

        var values = [];

        jQuery('.fsj_checklist_checkbox').each(function () {
            var field = jQuery(this).attr('field');
            if (field == input) {
                jQuery(this).attr('checked', 'checked');
                values.push(jQuery(this).val());
            }
        });

        jQuery('#' + input).val(values.join(";"));
    });

    jQuery('.fsj_checklist_uncheckall').click(function (ev) {
        ev.preventDefault();

        var input = jQuery(this).attr('field');

        jQuery('#' + input).val("");

        jQuery('.fsj_checklist_checkbox').each(function () {
            var field = jQuery(this).attr('field');
            if (field == input) {
                jQuery(this).removeAttr('checked');
            }
        });

    });
});

function fsj_checklist_showhide(show, id) {
    if (show) {
        jQuery('#' + id + '-tr').show();
        jQuery('#' + id + '-cont').show();
        jQuery('#' + id + '-lbl').show();
    } else {
        jQuery('#' + id + '-tr').hide();
        jQuery('#' + id + '-cont').hide();
        jQuery('#' + id + '-lbl').hide();
    }
}