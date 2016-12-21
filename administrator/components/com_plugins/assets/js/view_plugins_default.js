//huong dan su dung
/*
 $('.view_plugins_default').view_plugins_default();

 view_plugins_default=$('.view_plugins_default').data('view_plugins_default');
 console.log(view_plugins_default);
 */

// jQuery Plugin for SprFlat admin view_plugins_default
// Control options and basic function of view_plugins_default
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {

    // here we go!
    $.view_plugins_default = function (element, options) {

        // plugin's default options
        var defaults = {
            view: ''
        }
        // current instance of the object
        var plugin = this;
        // this will hold the merged default, and user-provided options
        plugin.settings = {}
        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element
        // the "constructor" method that gets called when the object is created
        plugin.edit_row = function ($row) {
            var extension_id = $row.data('extension_id');
            var option_click = {
                option: 'com_plugins',
                task: 'plugin.ajax_edit_row',
                format: 'json',
                tmpl: 'component',
                ignoreMessages: true
            };
            option_click = $.param(option_click);
            var data_submit = {};
            data_submit.extension_id = extension_id;
            var ajax_web_design = $.ajax({
                contentType: 'application/json',
                type: "POST",
                dataType: "json",
                url: root_ulr + '/index.php?' + option_click,
                data: JSON.stringify(data_submit),
                beforeSend: function () {
                    $('.div-loading').show();
                },
                success: function (response) {
                    $('.div-loading').hide();
                    $row.addClass('edit-row');
                    $row.find('td.include_menu').html(response.data.html_include_menu);
                    $row.find('td.include_menu select.menu').select2();
                    $row.find('td.exclude_menu').html(response.data.html_exclude_menu);
                    $row.find('td.exclude_menu select.menu').select2();
                    //$element.find('input[name="'+element_name+'"]').val(response.data);
                    $.scrollTo($row,1500, { offset:-300 });
                }
            });
        };
        plugin.delete_row = function ($row) {
            console.log('delete_row');
        };
        plugin.save_row = function ($row) {

            var option_click = {
                option: 'com_plugins',
                task: 'plugin.ajax_save_row',
                format: 'json',
                tmpl: 'component',
                ignoreMessages: true
            };
            option_click = $.param(option_click);
            var data_submit = {};
            var include_menu=$row.find('td.include_menu select.menu').val();
            var exclude_menu=$row.find('td.exclude_menu select.menu').val();
            var extension_id = $row.data('extension_id');
            data_submit.include_menu = include_menu;
            data_submit.exclude_menu = exclude_menu;
            data_submit.extension_id = extension_id;
            var ajax_web_design = $.ajax({
                contentType: 'application/json',
                type: "POST",
                dataType: "json",
                url: root_ulr + '/index.php?' + option_click,
                data: JSON.stringify(data_submit),
                beforeSend: function () {
                    $('.div-loading').show();
                },
                success: function (response) {
                    $('.div-loading').hide();
                    var data=response.data;
                    if(response.success==true)
                    {
                        $.notify('save successful', {
                            type: 'info'
                        });
                        $row.removeClass('edit-row');
                        $row.find('td.include_menu').html(data.list_menu_include.join(','));
                        $row.find('td.exclude_menu').html(data.list_menu_exclude.join(','));
                        $row.addClass('highlighted');
                        setTimeout(function(){
                            $row.removeClass('highlighted');}, 4000);

                        $.scrollTo($row,1500, { offset:-300 });
                    }
                    //$element.find('input[name="'+element_name+'"]').val(response.data);
                }
            });
        };
        plugin.cancel_row = function ($row) {

            var option_click = {
                option: 'com_plugins',
                task: 'plugin.ajax_cancel_row',
                format: 'json',
                tmpl: 'component',
                ignoreMessages: true
            };
            option_click = $.param(option_click);
            var data_submit = {};
            var include_menu=$row.find('td.include_menu select.menu').val();
            var exclude_menu=$row.find('td.exclude_menu select.menu').val();
            var extension_id = $row.data('extension_id');
            data_submit.extension_id = extension_id;
            var ajax_web_design = $.ajax({
                contentType: 'application/json',
                type: "POST",
                dataType: "json",
                url: root_ulr + '/index.php?' + option_click,
                data: JSON.stringify(data_submit),
                beforeSend: function () {
                    $('.div-loading').show();
                },
                success: function (response) {
                    $('.div-loading').hide();
                    var data=response.data;
                    if(response.success==true)
                    {
                        $.notify('save successful', {
                            type: 'info'
                        });
                        $row.removeClass('edit-row');
                        $row.find('td.include_menu').html(data.list_menu_include.join(','));
                        $row.find('td.exclude_menu').html(data.list_menu_exclude.join(','));
                        $row.addClass('highlighted');
                        setTimeout(function(){
                            $row.removeClass('highlighted');}, 4000);

                        $.scrollTo($row,1500, { offset:-300 });
                    }
                    //$element.find('input[name="'+element_name+'"]').val(response.data);
                }
            });
        };
        plugin.init = function () {
            plugin.settings = $.extend({}, defaults, options);
            var view = plugin.settings.view;
            var $view = $element.find('.' + view);
            $view.find(".edit-row").click(function (e) {
                var $row = $(this).closest('tr');
                var $tr_edit_row = $view.find('tr.edit-row');
                if ($tr_edit_row.length > 0) {
                    $.notify('you are editing other row !', {
                        type: 'Error'
                    });
                    $.scrollTo($tr_edit_row,1500, { offset:-100 });
                    return false;
                }
                plugin.edit_row($row);
            });
            $view.find(".delete-row").click(function (e) {
                var $row = $(this).closest('tr');
                plugin.delete_row($row);
            });
            $view.find(".save-row").click(function (e) {
                var $row = $(this).closest('tr');
                plugin.save_row($row);
            });
            $view.find(".cancel-row").click(function (e) {
                var $row = $(this).closest('tr');
                plugin.cancel_row($row);
            });
        }
        plugin.example_function = function () {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.view_plugins_default = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_plugins_default')) {
                var plugin = new $.view_plugins_default(this, options);
                $(this).data('view_plugins_default', plugin);
            }
        });
    }
})(jQuery);
