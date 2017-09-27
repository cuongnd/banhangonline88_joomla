//huong dan su dung
/*
 $('.icons_wrapper').icons_wrapper();
 icons_wrapper=$('.icons_wrapper').data('icons_wrapper');
 console.log(icons_wrapper);
 */
// jQuery Plugin for SprFlat admin icons_wrapper
// Control options and basic function of icons_wrapper
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {
    // here we go!
    $.icons_wrapper = function (element, options) {
        // plugin's default options
        var defaults = {
            selected:"",
            name:""
            //main color scheme for icons_wrapper
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
            var selected=plugin.settings.selected;
            var name=plugin.settings.name;
            var $list_element=$element.find('#awesome .span3,#glyphicons li,#themify .icon-container');
            for(var i=0;i<$list_element.length;i++){
                var $item=$($list_element.get(i));
                if($item.find('i').attr('class')==selected){
                    $item.addClass('tag');
                }else if($item.find('span[class^="ti-"][class*="ti-"]').attr('class')==selected){
                    $item.addClass('tag');
                }else if($item.find('span.glyphicon').attr('class')==selected){
                    $item.addClass('tag');
                }
            }
            $list_element.click(function(){
                var name=plugin.settings.name;
                $list_element.removeClass('tag');
                $item=$(this);
                $item.addClass('tag');

                var class_awesome=$item.find('i').attr('class');
                var class_themify=$item.find('span[class^="ti-"][class*="ti-"]').attr('class');
                var class_glyphicons=$item.find('span.glyphicon').attr('class');
                if(class_awesome){
                    $element.find('input[name="'+name+'"]').val(class_awesome);
                }else if(class_themify){
                    $element.find('input[name="'+name+'"]').val(class_themify);
                }else{
                    $element.find('input[name="'+name+'"]').val(class_glyphicons);
                }

            });
            $element.find('select.enable').change(function(){
                var name=plugin.settings.name;
                var value=$(this).val();
                if(value==0){
                    $list_element.removeClass('tag');
                    $element.find('input[name="'+name+'"]').val("");
                }
            });
        }
        plugin.example_function = function () {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.icons_wrapper = function (options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {
            // if plugin has not already been attached to the element
            if (undefined == $(this).data('icons_wrapper')) {
                var plugin = new $.icons_wrapper(this, options);
                $(this).data('icons_wrapper', plugin);
            }
        });
    }
})(jQuery);
