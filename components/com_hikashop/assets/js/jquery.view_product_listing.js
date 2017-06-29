//huong dan su dung
/*
 $('.view_product_listing').view_product_listing();

 view_product_listing=$('.view_product_listing').data('view_product_listing');
 console.log(view_product_listing);
 */

// jQuery Plugin for SprFlat admin view_product_listing
// Control options and basic function of view_product_listing
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_product_listing = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for view_product_listing
            //be sure to be same as colors on main.css or custom-variables.less
            link:'',
            option_dreymodal : {
                minWidth: 250,
                maxWidth: 250,
                overlay: true,
                overlayColor: "#222222",
                overlayOpacity: 0.9,
                closeButton: true,
                inAnimationTime: 600,
                inAnimationType: "slideInFromLeft",
                outAnimationTime: 600,
                outAnimationType: "slideOutToRight",
                allowEscapeKey: true,
                title: "Alert",
                titleBackColor: "#128a4b",
                overlayBlur: false,
                append: false
            }
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
            var link=plugin.settings.link;
            var option_dreymodal=plugin.settings.option_dreymodal;
            $element.find('.hikashop_products .hikashop_product .facebook-like').click(function(){
                var alert_drey_modal = new Dreymodal('<div>dfdfdfd</div>', option_dreymodal);
                alert_drey_modal.open();
            });
/*
            $element.find('.hikashop_products .hikashop_product').hover(function(){
                var link_current_product=$(this).find('.hikashop_product_name a').attr('href');
                var link_current_product=root_ulr+link_current_product;
                link_current_product=link+link_current_product;
                $facebook_shared=$(this).find('iframe.facebook_shared');
                if($facebook_shared.length==0) {
                    var $fb_area_like = $(this).find('.fb-like');
                    var $facebook_shared = $('<iframe class="facebook_shared" src="' + link_current_product + '"></iframe>');
                    $facebook_shared.appendTo($fb_area_like);
                }
            });
*/


        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_product_listing = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_product_listing')) {
                var plugin = new $.view_product_listing(this, options);

                $(this).data('view_product_listing', plugin);

            }

        });

    }

})(jQuery);
