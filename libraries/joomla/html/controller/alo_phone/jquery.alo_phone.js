//huong dan su dung
/*
 $('.alo_phone').alo_phone();
 alo_phone=$('.alo_phone').data('alo_phone');
 console.log(alo_phone);
 */
// jQuery Plugin for SprFlat admin alo_phone
// Control options and basic function of alo_phone
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function($) {
    // here we go!
    $.alo_phone = function(element, options) {
        // plugin's default options
        var defaults = {
            //main color scheme for alo_phone
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
    $.fn.alo_phone = function(options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {
            // if plugin has not already been attached to the element
            if (undefined == $(this).data('alo_phone')) {
                var plugin = new $.alo_phone(this, options);
                $(this).data('alo_phone', plugin);
            }
        });
    }
})(jQuery);
