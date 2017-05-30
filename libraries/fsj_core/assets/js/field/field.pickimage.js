/*
Javascript for image picking field type
*/

var current_img_pick = '';

jQuery(document).ready(function () {
    jQuery('.img_pick_button').click(function (ev) {
        ev.preventDefault();
        // need to show image pick dialog
        current_img_pick = jQuery(this).attr('id').split('|')[1];
        var url = jQuery(this).attr('href');
        var value = jQuery('#' + current_img_pick).val();
        var pathIndex = value.lastIndexOf("/") + 1;
        if (pathIndex) {
            var path = value.substr(0, pathIndex - 1);
            url += "&path=" + encodeURIComponent(path);
        }

        jQuery('#fsj_modal').unbind("hidden");

        var onclose = jQuery(this).attr('data_modal_close');
        if (onclose) {
            jQuery('#fsj_modal').on('hidden', function () {
                eval(onclose);
            });
        }

        jQuery('#fsj_modal').addClass('iframe');
        jQuery('#fsj_modal').html("<iframe src='" + url + "' seamless='seamless' style='width:100%;'>");
        jQuery('#fsj_modal').modal("show");
        jQuery('#fsj_modal').css('width', '800px');
        jQuery('#fsj_modal').css('margin-left', '-400px');
        //TINY.box.show({ iframe: url, width: 800, height: 590 });
    });
});

function ImgPickChoose(image) {
    jQuery('#fsj_modal').modal('hide');

    var preview = jQuery('#img_' + current_img_pick + '_preview');
    var label = jQuery('#img_' + current_img_pick + '_name');
    var input = jQuery('#' + current_img_pick);
    var link = jQuery('#img_' + current_img_pick + '_link');
    if (image == "") {
        image = "libraries/fsj_core/assets/images/misc/pick_image/no_image-64.png";
        preview.css('background-image', "url('" + img_pick_sitebase + image + "')");
        link.attr('href', img_pick_sitebase + image);
        label.text("None");
        input.val("");
    } else {
        preview.css('background-image', "url('" + img_pick_sitebase + 'images/' + image + "')");
        var img_label = image.replaceAll("/", " / ");
        label.text(img_label);
        link.attr('href', img_pick_sitebase + 'images/' + image);
        input.val(image);
       /* jQuery(a).attr('title', image);
        jQuery(a).attr('href', img_pick_sitebase + 'images/' + image);
        jQuery(img).attr('src', img_pick_sitebase + 'images/' + image);
        jQuery('#' + current_img_pick + '_name').text(image);
        jQuery('#' + current_img_pick).val(image);*/
    }
}