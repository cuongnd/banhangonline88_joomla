//huong dan su dung
/*
 $('.view_category_listing').view_category_listing();

 view_category_listing=$('.view_category_listing').data('view_category_listing');
 console.log(view_category_listing);
 */

// jQuery Plugin for SprFlat admin view_category_listing
// Control options and basic function of view_category_listing
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_category_listing = function(element, options) {

        // plugin's default options
        var defaults = {
            vatgia_link:''
            //main color scheme for view_category_listing
            //be sure to be same as colors on main.css or custom-variables.less

        }

        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        plugin.settings = {}

        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element

        // the "constructor" method that gets called when the object is created
        plugin.get_alias = function() {

        };
        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            var vatgia_link=plugin.settings.vatgia_link;
            $element.find('input.vatgia').click(function(){
                $adminForm=$element.find('form#adminForm');
                $adminForm.submit();
            });
        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_category_listing = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_category_listing')) {
                var plugin = new $.view_category_listing(this, options);

                $(this).data('view_category_listing', plugin);

            }

        });

    }

})(jQuery);
