//huong dan su dung
/*
 $('.mod_search').mod_search();

 mod_search=$('.mod_search').data('mod_search');
 console.log(mod_search);
 */

// jQuery Plugin for SprFlat admin mod_search
// Control options and basic function of mod_search
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.mod_search = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for mod_search
            //be sure to be same as colors on main.css or custom-variables.less
            module_id:0,
            style:"table",
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
            var params=plugin.settings.params;
            console.log(params);
            $element.find('.search').zozoTabs({
                theme: params.theme ,
                style: params.style|"clean" ,
                orientation: params.orientation ,
                position: params.position ,
                size: "medium",
                animation: {
                    easing: "easeInOutExpo",
                    duration: 400,
                    effects: params.effects
                },
                modes:"menu",
                event: params.event,
                classes: params.classes,
                defaultTab:"tab1",
                multiline: false,
                rounded:  (typeof params.zozo_rounded === "undefined") ? false : (params.zozo_rounded=="true"?true:false) ,
                mobileNav:  (typeof params.mobileNav === "undefined") ? false : (params.mobileNav=="true"?true:false) ,
                multiline:  (typeof params.multiline === "undefined") ? false : (params.multiline=="true"?true:false) ,
                rememberState:  (typeof params.rememberState === "undefined") ? true : (params.rememberState=="true"?true:false) ,
                shadows:  (typeof params.shadows === "undefined") ? true : (params.shadows=="true"?true:false) ,
                minWindowWidth:  params.minWindowWidth|200 ,
                size:  params.size|200 ,
                maxRows: params.maxRows||"xxlarge"
            });
            var $search_by_group_product=$element.find('.wrapper-search .search-by-group-product');
            $search_by_group_product.click(function(event){
                return;
                var position = $search_by_group_product.offset();
                var $dropdown_menu_list_group_product=$(document).find('.list-group-product');
                $dropdown_menu_list_group_product.show().

                // In the right position (the mouse)
                css({
                    top: position.left + "px",
                    left: position.top + "px"
                });

            });


            var $dropdown_menu_list_group_product=$element.find('.wrapper-search .list-group-product');
            $dropdown_menu_list_group_product.appendTo('body');




        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.mod_search = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('mod_search')) {
                var plugin = new $.mod_search(this, options);

                $(this).data('mod_search', plugin);

            }

        });

    }

})(jQuery);
