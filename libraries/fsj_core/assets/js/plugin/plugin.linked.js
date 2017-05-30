jQuery(document).ready(function () {
    fsj_<?php echo $this->id; ?>_remove_events();
    jQuery('#fsj_<?php echo $this->id; ?>_add').click(function (ev) {
        ev.preventDefault();
        var url = fsj_<?php echo $this->id; ?>_url;
        TINY.box.show({ iframe: url, width: 800, height: 500, animate: false, iframeresize: true });
    });
    fsj_attachment_SetSort();
});

function fsj_<?php echo $this->id; ?>_remove_events() {
    jQuery('.fsj_<?php echo $this->id; ?>_remove').unbind('click');
    jQuery('.fsj_<?php echo $this->id; ?>_remove').click(function (ev) {
        ev.preventDefault();
        var id = jQuery(this).attr('id');
        var pluginid = id.split('_')[1];
        var sourceid = id.split('_')[2];

        var removediv = "<?php echo $this->id; ?>div_" + pluginid + "_" + sourceid;

        jQuery('#' + removediv).remove();

        var value = jQuery('#fsj_<?php echo $this->id; ?>_values').val();
        var parts = value.split('&');
        var find = pluginid + "=" + sourceid;
        value = "";
        for (var i = 0; i < parts.length; i++) {
            if (parts[i] == find)
                continue;

            value += parts[i] + "&";
        }
        if (value.length > 0)
            value = value.substr(0, value.length - 1);
        jQuery('#fsj_<?php echo $this->id; ?>_values').val(value);
    });

    if(typeof fsj_<?php echo $this->id; ?>_SetSort == 'function')
    {
        fsj_<?php echo $this->id; ?>_SetSort();
    }
}

function fsj_<?php echo $this->id; ?>_SetSort()
{
    try
    {
        jQuery('#fsj_<?php echo $this->id; ?>_items').sortable({
            handle: '.handle'
        }).unbind('sortupdate').bind('sortupdate', function () {
            var items = new Array();

            jQuery('#fsj_<?php echo $this->id; ?>_items').children().each(function () {
                var id = jQuery(this).attr('id').split('_');
                var plugin = id[1];
                var itemid = id[2];
                items[items.length] = plugin + "=" + itemid;
            });

            jQuery('#fsj_<?php echo $this->id; ?>_values').val(items.join("&"));
        });
    } catch (e) {

    }
}

function Add<?php echo $this->id; ?>Items(pluginid, ids) {
    TINY.box.hide();

    var url = fsj_<?php echo $this->id; ?>_lookup_url + '&pluginid=' + pluginid + '&ids=' + ids.join(',');
    jQuery.get(url, function (data) {
        var data = JSON.parse(data);
        for (item in data) {
            item = data[item];
            // need to add the html to the div containing items, and the data to the field
            jQuery('#fsj_<?php echo $this->id; ?>_items').append(item.html);
            var val = "&" + pluginid + "=" + item.dest_id;
            jQuery('#fsj_<?php echo $this->id; ?>_values').val(jQuery('#fsj_<?php echo $this->id; ?>_values').val() + val);
        }
        fsj_<?php echo $this->id; ?>_remove_events();
    });
}

function Add<?php echo $this->id; ?>ParamItem(pluginid, params) {
    TINY.box.hide();
    params = JSON.stringify(params);
    var pleasewaitdiv = jQuery("<div>");
    pleasewaitdiv.html("<span><?php echo JText::_('PLEASE_WAIT'); ?></span>");
    pleasewaitdiv.addClass('fsj_linked_please_wait');
    jQuery('#fsj_<?php echo $this->id; ?>_items').append(pleasewaitdiv);

    var url = fsj_<?php echo $this->id; ?>_param_url + '&pluginid=' + pluginid + '&params=' + encodeURIComponent(params);
    jQuery.get(url, function (data) {
        var data = JSON.parse(data);
        pleasewaitdiv.remove();
        jQuery('#fsj_<?php echo $this->id; ?>_items').append(data.html);
        var val = "&" + pluginid + "=" + data.dest_id;
        jQuery('#fsj_<?php echo $this->id; ?>_values').val(jQuery('#fsj_<?php echo $this->id; ?>_values').val() + val);
        fsj_<?php echo $this->id; ?>_remove_events();
     });
}