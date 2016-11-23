//huong dan su dung
/*
 $('.mod_pageload').mod_pageload();

 mod_pageload=$('.mod_pageload').data('mod_pageload');
 console.log(mod_pageload);
 */

// jQuery Plugin for SprFlat admin mod_pageload
// Control options and basic function of mod_pageload
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.mod_pageload = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for mod_pageload
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
            $element.find('a.tim-hieu-website').click(function(){
                var load_page=$('body').data('load_page');
                load_page.settings.show_help=true;
                if(load_page.settings.show_help) {
                    load_page.set_help();
                    var help_tour = introJs();
                    help_tour.setOption('tooltipPosition', 'auto');
                    help_tour.setOption('teletype', true);
                    help_tour.setOption('enable_audio', true);
                    help_tour.setOption('positionPrecedence', ['left', 'right', 'bottom', 'top']);
                    help_tour.start();
                }

            });
            $element.find('input[type="checkbox"]').iCheck({
                checkboxClass: 'icheckbox_flat',
                radioClass: 'iradio_flat'
            });

        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.mod_pageload = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('mod_pageload')) {
                var plugin = new $.mod_pageload(this, options);

                $(this).data('mod_pageload', plugin);

            }

        });

    }

})(jQuery);
