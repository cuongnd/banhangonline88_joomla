
var category_change = [];
var category_raw_to_item = {};
var category_item_to_raw = {};

jQuery(document).ready(function () {
    // Joomla 3.x
    jQuery('#toolbar-folder button').removeAttr("onclick");
    jQuery('#toolbar-folder button').unbind("click");
    jQuery('#toolbar-folder button').click(function () {
        if (document.adminForm.boxchecked.value == 0) {
            alert('Please first make a selection from the list');
        } else {
            change_category_list();
        }
    });

    // Joomla 2.5
    jQuery('#toolbar-folder a').removeAttr("onclick");
    jQuery('#toolbar-folder a').unbind("click");
    jQuery('#toolbar-folder a').click(function () {
        if (document.adminForm.boxchecked.value == 0) {
            alert('Please first make a selection from the list');
        } else {
            change_category_list();
        }
    });

    jQuery('.cat_btn').each(function () {
        var item_id = jQuery(this).attr('item_id');
        var raw_id = jQuery(this).attr('raw_id');

        category_item_to_raw[item_id] = raw_id;
        category_raw_to_item[raw_id] = item_id;
    });
    var k = 0;
});


function change_category_btn(button) {
    category_change.length = 0;
    var key = jQuery(button).attr('item_id');
    category_change.push(key);
    change_cateogry_show();
}

function change_category_list() {
    // need to find all checked items
    var cbid = 0;
    var cb = jQuery('#cb' + cbid);

    category_change.length = 0;

    while (cb.length > 0) {

        if (cb.is(':checked')) {
            var value = cb.attr('value');
            category_change.push(category_raw_to_item[value]);
            //cb.removeAttr('checked');
        }

        cbid++;
        cb = jQuery('#cb' + cbid);
    }

    change_cateogry_show();
}

function change_cateogry_show() {
    jQuery('#category-modal-list').html("");
    jQuery('#filter_category option').each(function () {
        var value = jQuery(this).attr('value');
        var text = jQuery(this).text();

        if (value == "")
            return;

        var a = jQuery('<a />');
        a.text(text);
        a.addClass('category-modal-item');
        a.attr('href', '#');
        a.attr('value', value);

        var li = jQuery('<li />');
        li.append(a);

        jQuery('#category-modal-list').append(li);
    });

    jQuery('.category-modal-item').click(function (ev) {
        ev.preventDefault();
        category_save(jQuery(this).attr('value'));
    });

    jQuery('#category-modal-new').val("");

    jQuery('#category-modal').modal();
}

function category_save(newcat) {
    jQuery('#category-modal').modal('hide');

    var url = "index.php?option=com_fsj_transman&task=file.category&category=" + encodeURIComponent(newcat);

    var saving = [];

    for (i = 0; i < category_change.length; i++) {
        var key = category_change[i];

        saving.push(key);

        var raw = category_item_to_raw[key];

        url += "&file" + i + "=" + encodeURIComponent(raw);

        if (newcat == "--none--") {
            if (jQuery('#base_cat_' + key).length > 0) {
                var base = jQuery('#base_cat_' + key).html();
                if (base.trim().length > 0) {
                    jQuery('#cat_' + key).html("<span class='badge badge-info pull-right'>i</span> <span class='muted'>" + base + "</span>");
                } else {
                    jQuery('#cat_' + key).html("");
                }
            } else {
                jQuery('#cat_' + key).html("");
            }
        } else {
            jQuery('#cat_' + key).html(newcat);
        }

        jQuery('#cat_cont_' + key).hide();
        jQuery('#cat_wait_' + key).show();
    }

    jQuery.get(url, function (data) {
        for (var i = 0; i < saving.length; i++) {
            var key = saving[i];
            jQuery('#cat_cont_' + key).show();
            jQuery('#cat_wait_' + key).hide();
        }
    });
}

function category_save_new() {
    jQuery('#category-modal').modal('hide');

    var cat = jQuery('#category-modal-new').val();
    category_save(cat);

    jQuery('#filter_category').append("<option value='" + cat + "'>" + cat + "</option>");
}