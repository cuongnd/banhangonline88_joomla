
jQuery(document).ready(function () {
    fsj_init_elements();

    jQuery('#fsj_modal_container').appendTo(document.body);

    // fix hide event in bootstrap when mootools is loaded 
    if (typeof (MooTools) != "undefined") {
        (function ($) {
            $$('[data-toggle=collapse]').each(function (e) {
                if ($$(e.get('data-target')).length > 0) {
                    $$(e.get('data-target'))[0].hide = null;
                }
            });
        })(MooTools);
    }
});

function fsj_init_elements() {

    jQuery('.fsj_vertcenter').each(function () {
        var elem_height = jQuery(this).outerHeight(true);
        var parent_height = jQuery(this).parent().height();
        var offset = parseInt((parent_height - elem_height) / 2);
        jQuery(this).css('top', offset + 'px');
    });

    jQuery('.fsj_show_modal').click(function (ev) {
        ev.preventDefault();
        var url = jQuery(this).attr('href');
        var width = jQuery(this).attr('data_modal_width');
        if (typeof (width) != "number")
            width = 0;
        if (width < 1)
            width = 560;

        if (jQuery(window).width() < 766) {
            width = jQuery(window).width();
        }

        var offset = parseInt(width / 2);

        jQuery('#fsj_modal').unbind('hidden');

        jQuery('#fsj_modal').html(jQuery('#fsj_modal_base').html());
        jQuery('#fsj_modal').css('width', width + 'px');
        jQuery('#fsj_modal').css('margin-left', '-' + offset + 'px');
        jQuery('#fsj_modal').modal("show");
        jQuery('#fsj_modal').load(url);
    });

    jQuery('.fsj_show_modal_window').click(function (ev) {
        ev.preventDefault();
 
        var url = jQuery(this).attr('href');
 
        var width = jQuery(this).attr('data_window_width');
        var height = jQuery(this).attr('data_window_height');

        if (typeof(width) == "undefined")
            width = 560;

        if (typeof(height) == "undefined")
            height = 480;

        if (typeof (width) == "string" && width.indexOf('%') > 0) {
            width = width.replace("%", "");
            width = parseInt(width / 100 * jQuery(window).width());
        }

        if (typeof (height) == "string" && height.indexOf('%') > 0) {
            height = height.replace("%", "");
            height = parseInt(height / 100 * jQuery(window).height());
        }

        window.open(url, "_newwindow", "toolbar=no,width=" + width.toString() + ",height=" + height.toString() + ",status=no");
    });

    jQuery('.fsj_show_modal_iframe').click(function (ev) {
        ev.preventDefault();
        var url = jQuery(this).attr('href');
        var width = jQuery(this).attr('data_modal_width');

        if (typeof(width) == "undefined")
            width = 560;

        if (typeof(width) == "string" && width.indexOf('%') > 0) {
            width = width.replace("%", "");
            width = parseInt(width / 100 * jQuery(window).width());
        }

        if (width > parseInt(jQuery(window).width() * 0.95))
            width = parseInt(jQuery(window).width() * 0.95);

        var offset = parseInt(width / 2);

        var onclose = jQuery(this).attr('data_modal_close');

        jQuery('#fsj_modal').unbind("hidden");

        if (onclose) {
            jQuery('#fsj_modal').on('hidden', function () {
                eval(onclose);
            });
        }

        jQuery('#fsj_modal').addClass('iframe');
        jQuery('#fsj_modal').html("<iframe src='" + url + "' seamless='seamless' class='fsj_iframe'>");
        jQuery('#fsj_modal').modal("show");
        jQuery('#fsj_modal').css('width', width + 'px');
        jQuery('#fsj_modal').css('margin-left', '-' + offset + 'px');
        //jQuery('#fsj_modal').css('margin-top', '-250px');
    });

    jQuery('.fsj_show_modal_image').click(function (ev) {
        ev.preventDefault();
        var url = jQuery(this).attr('href');

        jQuery('#fsj_modal').addClass('iframe');

        var html = "<img id='modal_image_wait' src='" + jQuery('#fsj_base_url').text() + "/components/com_fsj/assets/images/ajax-loader.gif' style='padding:84px;'>";
        html += "<img id='modal_image_image' src='" + url + "' style='display: none'>";
        html += "<div class='modal_image_close'>&times;</div>";

        jQuery('#fsj_modal').html(html);
        jQuery('#fsj_modal').modal("show");
        jQuery('#fsj_modal').css('width', '200px');
        jQuery('#fsj_modal').css('margin-left', '-100px');
        //jQuery('#fsj_modal').css('margin-top', '-250px');

        jQuery('#fsj_modal').unbind('hidden');

        jQuery('.modal_image_close').click(function () {
            jQuery('#fsj_modal').modal("hide");
        });

        jQuery('#modal_image_image').load(function () {
            var width = this.width;
            var height = this.height;

            var max_width = parseInt(jQuery(window).width() * 0.9);
            var max_height = parseInt(jQuery(window).height() * 0.9);

            if (width > max_width) {
                var scale = max_width / width;
                width = parseInt(scale * width);
                height = parseInt(scale * height);
            }

            if (height > max_height) {
                var scale = max_height / height;
                width = parseInt(scale * width);
                height = parseInt(scale * height);
            }

            var w_offset = parseInt(width / 2);
            var h_offset = parseInt(height / 2);
            jQuery('#modal_image_wait').hide();
            jQuery('#modal_image_image').show();

            if (jQuery.isFunction(jQuery('#fsj_modal').animate)) {
                jQuery('#fsj_modal').animate({ width: width + 'px', marginLeft: '-' + w_offset + 'px', marginTop: '-' + h_offset + 'px' }, 500);
            } else {
                jQuery('#fsj_modal').css('width', width + 'px');
                jQuery('#fsj_modal').css('margin-left', '-' + w_offset + 'px');
                jQuery('#fsj_modal').css('margin-top', '-' + h_offset + 'px');
            }
        });
    });

    jQuery('.fsj_selectcolor').change(function (ev) {
        fsj_select_update_color(this);
    });

    jQuery('.fsj_selectcolor').each(function () {
        fsj_select_update_color_init(this);
        fsj_select_update_color(this);
    });

    if (jQuery.fn.fsj_tooltip)
        jQuery('.fsjTip').fsj_tooltip();
}

function fsj_select_update_color_init(el) {
    var sel_el = jQuery(el);
    var value = sel_el.val();
    // change color of dropdown

    basecol = sel_el.css('color');

    sel_el.css('color', sel_el.css('color'));

    sel_el.find('option').each(function () {
        var active = false;
        if (value == jQuery(this).attr('value')) {
            sel_el.val(value + 1);
            active = true;
            jQuery(this).removeAttr('selected');
        }

        var color = jQuery(this).css('color');

        if (color == "rgb(255, 255, 255)") // hack for IE
            color = basecol;

        jQuery(this).attr('dropdown-color', color);
        jQuery(this).css('color', color);
        if (active)
            jQuery(this).attr('selected', 'selected');
    });


    sel_el.find('optgroup').each(function () {
        jQuery(this).css('color', jQuery(this).css('color'));
    });
    sel_el.val(value);
}

function fsj_select_update_color(el) {
    jQuery(el).css('color', '');
    jQuery(el).find('option').each(function () {
        if (jQuery(this).attr('value') == jQuery(el).val()) {
            jQuery(el).css('color', jQuery(this).attr('dropdown-color'));
        }
    });
}

function fsj_modal_show(url, iframe, width, onclose) {
    if (!width)
        width = 560;
    var offset = parseInt(width / 2);

    jQuery('#fsj_modal').css('width', width + 'px');
    jQuery('#fsj_modal').css('margin-left', '-' + offset + 'px');

    jQuery('#fsj_modal').unbind('hidden');

    if (onclose) {
        jQuery('#fsj_modal').on('hidden', function () {
            eval(onclose);
        });
    }

    if (iframe) {
        jQuery('#fsj_modal').addClass('iframe');
        jQuery('#fsj_modal').html("<iframe src='" + url + "' scrolling='no' seamless='seamless' class='fsj_iframe'>");
        jQuery('#fsj_modal').modal("show");
    } else {
        jQuery('#fsj_modal').removeClass('iframe');
        jQuery('#fsj_modal').html(jQuery('#fsj_modal_base').html());
        jQuery('#fsj_modal').modal("show");
        jQuery('#fsj_modal').load(url);
    }
}

function fsj_modal_hide() {
    jQuery('#fsj_modal').modal("hide");
}


(function (jQuery) {
    function _outerSetter(direction, args) {

        var $el = jQuery(this),
            $sec_el = jQuery(args[0]),
            dir = (direction == 'Height') ? ['Top', 'Bottom'] : ['Left', 'Right'],
            style_attrs = ['padding', 'border'],
            style_data = {};
        // If we are detecting margins
        if (args[1]) {
            style_attrs.push('margin');
        }
        jQuery(style_attrs).each(function () {
            var $style_attrs = this;
            jQuery(dir).each(function () {
                var prop = $style_attrs + this + (($style_attrs == 'border') ? 'Width' : '');
                style_data[prop] = parseFloat($sec_el.css(prop));
            });
        });
        $el[direction.toLowerCase()]($sec_el[direction.toLowerCase()]());
        $el.css(style_data);
        return $el['outer' + direction](args[1]);

    }
    jQuery(['Height', 'Width']).each(function () {
        var old_method = jQuery.fn['outer' + this];
        var direction = this;
        jQuery.fn['outer' + this] = function () {
            if (typeof arguments[0] === 'string') {
                return _outerSetter.call(this, direction, arguments);
            }
            return old_method.apply(this, arguments);
        }
    });
})(jQuery);
