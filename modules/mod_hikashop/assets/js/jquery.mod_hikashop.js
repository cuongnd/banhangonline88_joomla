//huong dan su dung
/*
 $('.mod_hikashop').mod_hikashop();

 mod_hikashop=$('.mod_hikashop').data('mod_hikashop');
 console.log(mod_hikashop);
 */

// jQuery Plugin for SprFlat admin mod_hikashop
// Control options and basic function of mod_hikashop
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {

    // here we go!
    $.mod_hikashop = function (element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for mod_hikashop
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
            //$.set_height($element.find('.hikashop_listing_img_title'));
        }
        plugin.example_function = function () {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.mod_hikashop = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('mod_hikashop')) {
                var plugin = new $.mod_hikashop(this, options);
                $(this).data('mod_hikashop', plugin);
            }
        });
    }
})(jQuery);
