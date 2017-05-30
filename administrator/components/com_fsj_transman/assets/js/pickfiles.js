jQuery(document).ready(function () {
    jQuery('div.list_files a').mouseover(function () {
        jQuery(this).parent().find('div.files_popup').show();
    });
    jQuery('div.list_files a').mouseout(function () {
        jQuery(this).parent().find('div.files_popup').hide();
    });
});

function tm_pf_remove_file(file, client) {
    jQuery('div.pf_file').each(function () {
        var elem = jQuery(this);
        var e_client = elem.attr('client');
        if (e_client != client)
            return;

        var e_file = elem.attr('file');
        if (e_file != file)
            return;

        elem.remove();

        if (client) {
            var input = jQuery('#jform_adminfiles');
        } else {
            var input = jQuery('#jform_sitefiles');
        }

        var value = input.val();
        var values = value.split(";");
        var index = values.indexOf(file);
        if (index > -1)
            values.splice(index, 1);

        input.val(values.join(";"));
    });
}

function tm_pf_add_file(client, tag) {
    if (client) {
        var input = jQuery('#jform_adminfiles');
    } else {
        var input = jQuery('#jform_sitefiles');
    }

    var url = jQuery('#add_file_url').text();

    url = url.replace("XXCLIENTXX", client);
    url = url.replace("XXTAGXX", tag);
    url = url.replace("XXCURRENTXX", input.val());
    TINY.box.show({iframe:url,width:800,height:500,scrolling:true})
}

function tm_pf_add_file_do(file, client) {
    TINY.box.hide();
    //alert(file + "\n" + client);
    var target = jQuery('#pf_files_' + client);

    var html = "<div class='pf_file' client='" + client + "' file='" + file + "'>";
    html += "<span>" + file + "</span>&nbsp;&nbsp;";
    html += "<button class='btn btn-mini' onclick='tm_pf_remove_file(\"" + file + "\", " + client + "); return false;'>";
    html += "<i class='icon-delete'></i></button></div>";

    target.append(html);

    if (client) {
        var input = jQuery('#jform_adminfiles');
    } else {
        var input = jQuery('#jform_sitefiles');
    }

    var value = input.val();
    var values = value.split(";");

    values[values.length] = file;

    value = values.join(';');

    input.val(value);
}

function tm_pf_add_files_do(files, client) {
    TINY.box.hide();
    for (i in files) {
        tm_pf_add_file_do(files[i], client);
    }
}