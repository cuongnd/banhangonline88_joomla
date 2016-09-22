//huong dan su dung
/*
 $('.productcategory').productcategory();

 productcategory=$('.productcategory').data('productcategory');
 console.log(productcategory);
 */

// jQuery Plugin for SprFlat admin productcategory
// Control options and basic function of productcategory
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.productcategory = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for productcategory
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
            $element.find( "span.item" ).click(function() {
                var $li=$(this).parent('li');
                var $ul=$li.find('>ul');
                if($ul.is(":visible")) {
                    $(this).switchClass("icon-folder-minus", "icon-folder-plus", 1000, "easeInOutQuad");
                    $ul.hide();
                }else{
                    $(this).switchClass("icon-folder-plus","icon-folder-minus", 1000, "easeInOutQuad");
                    $ul.show();
                }
            });
        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.productcategory = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('productcategory')) {
                var plugin = new $.productcategory(this, options);

                 $(this).data('productcategory', plugin);

            }

        });

    }

})(jQuery);
