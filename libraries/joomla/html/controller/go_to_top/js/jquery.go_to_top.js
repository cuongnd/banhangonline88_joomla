//huong dan su dung
/*
 $('.go_to_top').go_to_top();

 go_to_top=$('.go_to_top').data('go_to_top');
 console.log(go_to_top);
 */
 
// jQuery Plugin for SprFlat admin go_to_top
// Control options and basic function of go_to_top
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {

    // here we go!
    $.go_to_top = function (element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for go_to_top
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
    $.fn.go_to_top = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('go_to_top')) {
                var plugin = new $.go_to_top(this, options);
                $(this).data('go_to_top', plugin);
            }
        });
    }
})(jQuery);
