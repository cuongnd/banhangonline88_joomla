//huong dan su dung
/*
 $('.mod_products').mod_products();

 mod_products=$('.mod_products').data('mod_products');
 console.log(mod_products);
 */

// jQuery Plugin for SprFlat admin mod_products
// Control options and basic function of mod_products
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.mod_products = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for mod_products
            //be sure to be same as colors on main.css or custom-variables.less
            module_id:0,
            style:"table"

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
            $.set_height($element.find('.item .title'));
            var style=plugin.settings.style;
            var module_id=plugin.settings.module_id;
            if(style=='slider'){
                $element.find('.product_slide').slick({

                    // normal options...
                    infinite: true,
                    fade: true,
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    // the magic
                });


            }

        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.mod_products = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('mod_products')) {
                var plugin = new $.mod_products(this, options);

                $(this).data('mod_products', plugin);

            }

        });

    }

})(jQuery);
