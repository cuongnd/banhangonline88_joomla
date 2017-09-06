 //huong dan su dung
/*
 $('.select_device').select_device();

 select_device=$('.select_device').data('select_device');
 console.log(select_device);
 */

// jQuery Plugin for SprFlat admin select_device
// Control options and basic function of select_device
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {

    // here we go!
    $.select_device = function (element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for select_device
            //be sure to be same as colors on main.css or custom-variables.less
        }
        // current instance of the object
        var plugin = this;
        // this will hold the merged default, and user-provided options
        plugin.settings = {}
        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element
        // the "constructor" method that gets called when the object is created
        plugin.init = function () {
            plugin.settings = $.extend({}, defaults, options);
            $element.find('.icon-desktop').webuiPopover({
                title: 'Vui lòng lựa chọn thiết bị',
                content: function(){
                    return $element.find('#popover_content_wrapper_device_phone').html();
                },
                placement: 'top',
                closeable:true,
                style:'popover_select_device',
                onHide: function($element) {
                    $('.suntory-device-ph-img-circle .icon-desktop').show();
                }, // callback after hide
            });
        }
        plugin.example_function = function () {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.select_device = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('select_device')) {
                var plugin = new $.select_device(this, options);
                $(this).data('select_device', plugin);
            }
        });
    }
})(jQuery);
