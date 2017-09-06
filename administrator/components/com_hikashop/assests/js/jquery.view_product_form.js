//huong dan su dung
/*
 $('.view_product_form').view_product_form();

 view_product_form=$('.view_product_form').data('view_product_form');
 console.log(view_product_form);
 */

// jQuery Plugin for SprFlat admin view_product_form
// Control options and basic function of view_product_form
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_product_form = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for view_product_form
            //be sure to be same as colors on main.css or custom-variables.less
            auto_numeric_config:{
                mDec: 1,
                aSep: ' ',
                aSign: ''
            }
        }

        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        plugin.settings = {}

        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element

        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            auto_numeric_config=plugin.settings.auto_numeric_config;
            $element.find('.price').autoNumeric('init',auto_numeric_config);
            $element.find('.numeric').autoNumeric('init');

        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_product_form = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_product_form')) {
                var plugin = new $.view_product_form(this, options);

                $(this).data('view_product_form', plugin);

            }

        });

    }

})(jQuery);
