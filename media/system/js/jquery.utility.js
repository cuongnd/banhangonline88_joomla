(function ($) {
    $.tree_object = function (item, object_list, key_path) {
        if (typeof object_list == 'undefined') {
            var object_list = {};
        }

        if (!$.isEmptyObject(item)) {

            $.each(item, function (key, value) {
                if (typeof key_path !== 'undefined') {
                    var key_path1 = key_path + '.' + key;
                } else {
                    var key_path1 = key;
                }
                if (typeof value !== 'object') {
                    object_list[key_path1] = value
                } else if (!$.isEmptyObject(value)) {
                    $.tree_object(value, object_list, key_path1);
                }
            });
        }
        return object_list;
    };

    $.alert_warning_website_config = function (reset, current_step, count_error_ajax) {
        if (typeof reset == 'undefined') {
            reset = 0;
        }
        var data_submit = {};
        var option_click = {
            enable_load_component: 1,
            option: "com_website",
            task: "utility.ajax_alert_warning_website_config",
            reset: reset,
            current_step: current_step
        };
        option_click = $.param(option_click);
        $.ajax({
            contentType: 'application/json',
            type: "POST",
            dataType: "json",
            url: this_host + '/index.php?' + option_click,
            data: JSON.stringify(data_submit),
            beforeSend: function () {
                $('.div-loading').css({
                    display: "block"


                });
            },
            success: function (response) {
                $('.div-loading').css({
                    display: "none"


                });
                if (response.e == 0) {
                    if (response.finish == 0) {
                        current_step = response.current_step;
                        $.alert_warning_website_config('', current_step, count_error_ajax);
                    }
                } else if (response.e == 1) {
                    var notify = $.notify(response.m, {
                            allow_dismiss: false,
                            type: "warning"
                        }
                    );
                }
            },
            error: function (request, status, err) {
                if (status == "timeout") {
                    // timeout -> reload the page and try again
                    console.log("timeout");
                    $.alert_warning_website_config();
                } else {
                    if (count_error_ajax > 10) {
                        console.log('too many error ajax');
                    } else {
                        // another error occured
                        count_error_ajax++;
                        $.alert_warning_website_config(1, current_step, count_error_ajax);
                    }
                }
            }
        });

    };


    $.random = function (min, max) {
        min = parseInt(min);
        max = parseInt(max);
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    $.array_chunk = function (array, groupsize) {
        var sets = [], chunks, i = 0;
        chunks = array.length / groupsize;
        while (i < chunks) {
            sets[i] = array.splice(0, groupsize);
            i++;
        }
        return sets;
    };
    $.str_repeat = function (input, multiplier) {
        //  discuss at: http://phpjs.org/functions/str_repeat/
        // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
        // improved by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
        // improved by: Ian Carter (http://euona.com/)
        //   example 1: str_repeat('-=', 10);
        //   returns 1: '-=-=-=-=-=-=-=-=-=-='

        var y = '';
        while (true) {
            if (multiplier & 1) {
                y += input;
            }
            multiplier >>= 1;
            if (multiplier) {
                input += input;
            } else {
                break;
            }
        }
        return y;
    };
    $.isScrolledIntoView = function (elem) {
        var $elem = $(elem);
        var $window = $(window);

        var docViewTop = $window.scrollTop();
        var docViewBottom = docViewTop + $window.height();

        var elemTop = $elem.offset().top;
        var elemBottom = elemTop + $elem.height();

        return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    };
    $.makeid = function (length) {
        if (typeof length == 'undefined') {
            length = 5;
        }
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }
    $.fn.getOuterHTML = function () {
        var wrapper = $('<div class="getOuterHTML"></div>');
        $(this).wrap(wrapper);
        var html = $(this).parent().html();
        $(this).unwrap();
        return html;
    };
    $.fn.bho88loading = function (completed) {
        if (typeof completed == "undefined" || completed == false) {
            var $wrapper_loading = $('<div class="wrapper-loading"><div class="background"></div><img class="bho88loading" src="' + root_ulr + 'images/bho88.png" /></div>');
            $wrapper_loading.appendTo($(this));
            $(this).css({
                position: 'relative',

            });
            $wrapper_loading.css({
                position: 'absolute',
                width: '100%',
                height: '100%',
                'z-index': 9999
            });
            $wrapper_loading.find('.background').css({
                position: 'absolute',
                width: '100%',
                height: '100%',
                opacity: 0.7,
                background:'#fff'

            });
            $wrapper_loading.find('img.bho88loading').css({
                'vertical-align': 'middle',
                'text-align': 'center',
                position: 'absolute',
                top: '40%',
                left: '40%',
                'z-index': 9999
            });
        } else {
            $(this).css({
                position: 'static'

            });
            $(this).find('>.wrapper-loading').remove();
        }
    };
    $.fn.add_event_element = function (event, call_back_function, event_class) {
        $(this).each(function () {
            if (typeof event_class == 'undefined' || event_class.trim() == '') {
                throw 'there are no event class';
                return;
            }
            if (!$(this).hasClass(event_class)) {
                $(this).on(event, call_back_function).addClass(event_class);
            }
        });

    };
    $.fn.set_plugin_element = function (setup_plugin_function, setup_plugin_class, parameter) {
        $(this).each(function () {
            if (typeof setup_plugin_class == 'undefined' || setup_plugin_class.trim() == '') {
                throw 'there are no setup function class';
                return;
            }
            if (!$(this).hasClass(setup_plugin_class) && setup_plugin_function instanceof Function) {
                setup_plugin_function(parameter);
            }
        });

    };
    $.set_height = function ($elements) {
        var height = 0;
        for (var i = 0; i < $elements.length; i++) {
            var $element = $elements.eq(i);
            var element_height = $element.height();
            if (height < element_height) {
                height = element_height;
            }
        }
        $elements.height(height);

    };
    $.get_year_old_by_date = function (dateString) {
        if (dateString == '') {
            return '';
        }
        var today = new Date();
        var birthDate = new Date(dateString);
        var age = today.getFullYear() - birthDate.getFullYear();
        var m = today.getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    };
    $.randomDate = function (start, end) {
        return new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
    }
    $.getMultiScripts = function (arr, path) {
        var _arr = $.map(arr, function (scr) {
            return $.getScript((path || "") + scr);
        });

        _arr.push($.Deferred(function (deferred) {
            $(deferred.resolve);
        }));

        return $.when.apply($, _arr);
    }


})(jQuery);