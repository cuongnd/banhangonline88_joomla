//huong dan su dung

/*

 $('.view_items_edit').view_items_edit();



 view_items_edit=$('.view_items_edit').data('view_items_edit');

 console.log(view_items_edit);

 */



// jQuery Plugin for SprFlat admin view_items_edit

// Control options and basic function of view_items_edit

// version 1.0, 28.02.2013

// by SuggeElson www.suggeelson.com



(function($) {



    // here we go!

    $.view_items_edit = function(element, options) {



        // plugin's default options

        var defaults = {
            enable_check_params:false
            //main color scheme for view_items_edit

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
            var enable_check_params=plugin.settings.enable_check_params;
            if(enable_check_params) {
                $element.find('#toolbar-save .btn-small').click();
            }



        }



        plugin.example_function = function() {



        }

        plugin.init();



    }



    // add the plugin to the jQuery.fn object

    $.fn.view_items_edit = function(options) {



        // iterate through the DOM elements we are attaching the plugin to

        return this.each(function() {



            // if plugin has not already been attached to the element

            if (undefined == $(this).data('view_items_edit')) {

                var plugin = new $.view_items_edit(this, options);



                $(this).data('view_items_edit', plugin);



            }



        });



    }



})(jQuery);

