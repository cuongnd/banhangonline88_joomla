//huong dan su dung
/*
 $('.ajax_select_menu').ajax_select_menu();

 ajax_select_menu=$('.ajax_select_menu').data('ajax_select_menu');
 console.log(ajax_select_menu);
 */

// jQuery Plugin for SprFlat admin ajax_select_menu
// Control options and basic function of ajax_select_menu
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.ajax_select_menu = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for ajax_select_menu
            //be sure to be same as colors on main.css or custom-variables.less

        }

        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        plugin.settings = {}

        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element

        // the "constructor" method that gets called when the object is created
        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.ajax_select_menu = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('ajax_select_menu')) {
                var plugin = new $.ajax_select_menu(this, options);

                $(this).data('ajax_select_menu', plugin);

            }

        });

    }

})(jQuery);
