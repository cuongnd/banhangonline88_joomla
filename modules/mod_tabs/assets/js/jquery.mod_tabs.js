//huong dan su dung
/*
 $('.mod_tabs').mod_tabs();

 mod_tabs=$('.mod_tabs').data('mod_tabs');
 console.log(mod_tabs);
 */

// jQuery Plugin for SprFlat admin mod_tabs
// Control options and basic function of mod_tabs
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.mod_tabs = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for mod_tabs
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
            var params=plugin.settings.params;
            $element.find('.tabs').zozoTabs({
                theme: params.theme ,
                orientation: params.orientation ,
                position: params.position ,
                size: "medium",
                animation: {
                    easing: "easeInOutExpo",
                    duration: 400,
                    effects: params.effects
                },
                modes:"menu",
                select:function(event, item) {
                    var y = $(window).scrollTop();  //your current y position on the page
                    $(window).scrollTop(y+1);
                },
                event: params.event,
                classes: params.classes,
                defaultTab:"tab1",
                multiline:(typeof params.multiline === "undefined") ? false : (params.multiline=="true"?true:false)  ,
                rounded:  (typeof params.zozo_rounded === "undefined") ? false : (params.zozo_rounded=="true"?true:false) ,
                mobileNav:  (typeof params.mobileNav === "undefined") ? false : (params.mobileNav=="true"?true:false) ,
                multiline:  (typeof params.multiline === "undefined") ? false : (params.multiline=="true"?true:false) ,
                rememberState:  (typeof params.rememberState === "undefined") ? true : (params.rememberState=="true"?true:false) ,
                shadows:  (typeof params.shadows === "undefined") ? true : (params.shadows=="true"?true:false) ,
                minWindowWidth:  params.minWindowWidth|200 ,
                size:  params.size|"xxlarge",
                maxRows: params.maxRows||200,
                select: function(event, item) {
                    console.log($(item.tab));
                    $(item.tab.context).find('.flip-image').flip({
                        reverse: false,
                        trigger: "hover",
                        speed: 500,
                        forceHeight: true,
                        forceWidth: true,
                        autoSize: true

                    });
                }
            });




        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.mod_tabs = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('mod_tabs')) {
                var plugin = new $.mod_tabs(this, options);

                 $(this).data('mod_tabs', plugin);

            }

        });

    }

})(jQuery);
