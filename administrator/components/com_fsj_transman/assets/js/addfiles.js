
jQuery(document).ready(function () {
    rebuild_file_list();

    jQuery('#toolbar-plus button').attr('onclick', '');
    jQuery('#toolbar-plus button').unbind('click');
    jQuery('#toolbar-plus button').click(function (ev) {
        ev.preventDefault();
        jQuery('a[href="#form_tab_files"]').tab('show');
        jQuery('#add_files_button').click();
        return false;
    });

    jQuery('#jform_langcode').change(function () {
        var code = jQuery('#jform_langcode').val();
        jQuery('#add_files_button').attr('href', 'index.php?option=com_fsj_transman&view=pickfiles&tmpl=component&filter_element=' + code);
    });
});

function add_file_wrap(id, category, rebuild) {
    var bits = id.split("|");
    if (category == "") category = 'xxx-none-xxx';

    add_file(bits[0], bits[2], category, bits[3]);
    jQuery("#fsj_modal").modal("hide");

    if (rebuild)
        rebuild_file_list();
}

function add_file(client_id, group, category, filename) {
    var data = jQuery('#add_files_data').val();

    if (data == "[]")
        data = "{}";

    var parsed = jQuery.parseJSON(data);
    parsed = parsed || {};

    var key = client_id + "|" + group;

    parsed[key] = parsed[key] || {};


    parsed[key][category] = parsed[key][category] || [];

    var found = false;
    for (var i = 0; i < parsed[key][category].length; i++) {
        if (parsed[key][category][i] == filename)
            found = true;
    }

    if (!found)
        parsed[key][category].push(filename);

    data = JSON.stringify(parsed);

    jQuery('#add_files_data').val(data);
}

function rebuild_file_list() {
    var data = jQuery('#add_files_data').val();

    if (data == "[]")
        data = "{}";

    var ul = jQuery('#file_list');
    ul.html("");

    var html = "";
    var parsed = jQuery.parseJSON(data);

    if (!parsed)
        return;

    for (key in parsed) {
        var split = key.split("|");
        var title = "";
        var site = "";
        if (split[0] == 1) {
            site = tm_translate['admin'];
        } else {
            site = tm_translate['site'];
        }

        var type = split[1].split(".");
        if (type[0] == "g") {
            title += site;
        } else if (type[0] == "p") {
            title += tm_translate[type[0]] + " (" + type[1] + "): " + type[2];
        } else if (type[0] == "c" || type[0] == "m") {
            title += site + " " + tm_translate[type[0]] + ": " + type[1];
        } else if (type[0] == "t") {
            title += tm_translate[type[0]] + ": " + type[1];
        } else {
            title += tm_translate[type[0]] + ": " + type[1];
        }

        html += "<li class='folder'>" + title + "</li>";

        var subdata = parsed[key];

        for (category in subdata) {
            var items = subdata[category];

            categoryt = category;
            if (category == "xxx-none-xxx")
                categoryt = tm_translate['nocat'];

            html += "<li class='group'>" + categoryt + "</li>";

            for (var i = 0; i < items.length; i++) {
                var file = items[i];
                html += "<li key='" + key + "' filename='" + file + "' class='file' category='" + category + "'><a class='btn btn-micro btn-danger pull-left' style='margin-right: 4px;'><i class='icon-remove'></i></a>" + file + "</li>";
            }
        }
    }

    ul.append(html);

    jQuery('li.file .icon-remove').click(function (ev) {
        var li = jQuery(this).parent().parent();
        var filename = li.attr('filename');
        var key = li.attr('key');
        var category = li.attr('category');

        remove_file(key, category, filename);
    });
}

function remove_file(key, category, filename) {
    var data = jQuery('#add_files_data').val();

    if (data == "[]")
        data = "{}";

    var parsed = jQuery.parseJSON(data);

    for (var i = 0; i < parsed[key][category].length; i++) {
        if (parsed[key][category][i] == filename) {
            parsed[key][category].splice(i, 1);
        }
    }

    if (parsed[key][category].length == 0) {
        delete parsed[key][category];
    }

    var count = 0;
    for (category in parsed[key]) {
        count++;
    }
    if (count == 0)
        delete parsed[key];

    data = JSON.stringify(parsed);

    jQuery('#add_files_data').val(data);

    rebuild_file_list();
}