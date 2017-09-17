//huong dan su dung // jQuery Plugin for SprFlat admin location // Control options and basic function of location // version 1,28.02.2013 // by SuggeElson www.suggeelson.com (function ($){// here we go! $.location=function (element,options){// plugin's default options
        var defaults = {
            draggable:true
            //main color scheme for location
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
            $element.find(".gllpLatlonPicker").each(function () {
                var $obj = $(document).gMapsLatLonPicker();
                var draggable=plugin.settings.draggable;
                $obj.params.draggable = draggable;
                $obj.params.strings.markerText = "Drag this Marker (example edit)";
                $obj.params.displayError = function (message) {
                    console.log("MAPS ERROR: " + message); // instead of alert()
                };
                $obj.init($(this));
            });
        }
        plugin.example_function = function () {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.location = function (options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {
            // if plugin has not already been attached to the element
            if (undefined == $(this).data('location')) {
                var plugin = new $.location(this, options);
                $(this).data('location',plugin)}})}})(jQuery);