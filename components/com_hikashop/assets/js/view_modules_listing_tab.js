//huong dan su dung
/*
 $('.view_modules_listing_tab').view_modules_listing_tab();

 view_modules_listing_tab=$('.view_modules_listing_tab').data('view_modules_listing_tab');
 console.log(view_modules_listing_tab);
 */

// jQuery Plugin for SprFlat admin view_modules_listing_tab
// Control options and basic function of view_modules_listing_tab
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_modules_listing_tab = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for view_modules_listing_tab
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
            $element.find(".tabbed-nav").zozoTabs({
                theme: "silver",
                orientation: "horizontal",
                position: "top-left",
                size: "medium",
                animation: {
                    easing: "easeInOutExpo",
                    duration: 400,
                    effects: "slideH"
                },
                defaultTab: "tab3"
            });
        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_modules_listing_tab = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_modules_listing_tab')) {
                var plugin = new $.view_modules_listing_tab(this, options);

                $(this).data('view_modules_listing_tab', plugin);

            }

        });

    }

})(jQuery);
