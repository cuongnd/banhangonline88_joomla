//huong dan su dung
/*
 $('.mod_wishlist').mod_wishlist();

 mod_wishlist=$('.mod_wishlist').data('mod_wishlist');
 console.log(mod_wishlist);
 */

// jQuery Plugin for SprFlat admin mod_wishlist
// Control options and basic function of mod_wishlist
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.mod_wishlist = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for mod_wishlist
            //be sure to be same as colors on main.css or custom-variables.less
            module_id:0,
            style:"table",
            second:200,
            params:{}

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

            var timeout;
            var second=plugin.settings.second;
            $element.find('>.group_like_cart').hover(

                function () {
                    clearTimeout(timeout);
                    $(this).addClass('hover');
                    $element.find('.wrapper-wishlist').show();




                },

                function () {

                    $(this).removeClass('hover');
                    timeout = setTimeout(function() {
                        $element.find('.wrapper-wishlist').hide();
                    }, second);






                }

            );
            $element.find('.wrapper-wishlist').hover(function() {
                clearTimeout(timeout);
                $element.find('>.group_like_cart').addClass('hover');
            }, function() {
                timeout = setTimeout(function() {
                    $element.find('.wrapper-wishlist').hide();
                    $element.find('>.group_like_cart').removeClass('hover');
                }, second);

            });



        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.mod_wishlist = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('mod_wishlist')) {
                var plugin = new $.mod_wishlist(this, options);

                $(this).data('mod_wishlist', plugin);

            }

        });

    }

})(jQuery);
