//huong dan su dung // jQuery Plugin for SprFlat admin homepagetoprightmenu // Control options and basic function of homepagetoprightmenu // version 1,28.02.2013 // by SuggeElson www.suggeelson.com (function($){// here we go! $.homepagetoprightmenu=function(element,options){// plugin's default options
        var defaults = {
            //main color scheme for homepagetoprightmenu
            //be sure to be same as colors on main.css or custom-variables.less
            module_id:0,
            children_menu_item:{},
            root_url:"",
            lazyload:false,
            showing:false,
            max_item_level_2:20,
            max_item_level_3:5
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
            var module_id=plugin.settings.module_id;
            $element.find('.show-hide-menu').sidr({
                name: 'sidr_'+module_id,
                side: 'right', // By default
                displace:false
            });
            //$element.find('#sidr').addClass('hidden-desktop');

        }
        plugin.example_function = function() {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.homepagetoprightmenu = function(options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {
            // if plugin has not already been attached to the element
            if (undefined == $(this).data('homepagetoprightmenu')) {
                var plugin = new $.homepagetoprightmenu(this, options);
                $(this).data('homepagetoprightmenu',plugin)}})}})(jQuery);