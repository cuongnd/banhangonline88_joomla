//huong dan su dung
/*
 $('.show_screen_size').show_screen_size();

 show_screen_size=$('.show_screen_size').data('show_screen_size');
 console.log(show_screen_size);
 */

// jQuery Plugin for SprFlat admin show_screen_size
// Control options and basic function of show_screen_size
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {

    // here we go!
    $.show_screen_size = function (element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for show_screen_size
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
            $(window).resize(function () {
                $element.find('.size').html( $(window).width());
            });

        }
        plugin.example_function = function () {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.show_screen_size = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('show_screen_size')) {
                var plugin = new $.show_screen_size(this, options);
                $(this).data('show_screen_size', plugin);
            }
        });
    }
})(jQuery);
