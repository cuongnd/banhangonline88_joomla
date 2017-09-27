//huong dan su dung
/*
 $('.dashboard_template').dashboard_template();
 dashboard_template=$('.dashboard_template').data('dashboard_template');
 console.log(dashboard_template);
 */
// jQuery Plugin for SprFlat admin dashboard_template
// Control options and basic function of dashboard_template
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {
    // here we go!
    $.dashboard_template = function (element, options) {
        // plugin's default options
        var defaults = {
            selected:"",
            name:""
            //main color scheme for dashboard_template
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

        }
        plugin.example_function = function () {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.dashboard_template = function (options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {
            // if plugin has not already been attached to the element
            if (undefined == $(this).data('dashboard_template')) {
                var plugin = new $.dashboard_template(this, options);
                $(this).data('dashboard_template', plugin);
            }
        });
    }
})(jQuery);
