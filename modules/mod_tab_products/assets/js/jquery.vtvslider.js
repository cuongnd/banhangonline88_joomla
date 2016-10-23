//huong dan su dung
/*
 $('.vtvslider').vtvslider();

 vtvslider=$('.vtvslider').data('vtvslider');
 console.log(vtvslider);
 */

// jQuery Plugin for SprFlat admin vtvslider
// Control options and basic function of vtvslider
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.vtvslider = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for vtvslider
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
        plugin.update_product=function(list_product){
            if(list_product.length==0)
                return false;
            var template_item=plugin.settings.template_item;
            plugin.settings.list_item=[];
            for(var i=0;i<list_product.length;i=i+plugin.settings.total_show){
                var chunks=[];
                for(var j=i;j<i+plugin.settings.total_show;j++){
                    var item=list_product[j];
                    if(typeof item!="undefined") {
                        console.log(item);
                        var $template_item=$(plugin.settings.template_item);
                        $template_item.find('.slide.item').removeClass('test');
                        $template_item.find('img.image').attr('src',item.src);




                        $template_item.find('.price').html(item.price_format);
                        $template_item.find('.product-name a').attr('title',item.product_name);
                        $template_item.find('.product-name a').html(item.product_name);
                        chunks.push($template_item.getOuterHTML());
                    }
                }
                plugin.settings.list_item.push(chunks);
            }
            var $list_product=$element.find('.list-product');
            $list_product.find('>.slide.item').remove();
            var first_list_item=plugin.settings.list_item[0];

            for(var i=0;i<first_list_item.length;i++){
                var item=first_list_item[i];
                $(item).appendTo($list_product);
            }
            console.log(plugin.settings.list_item);
            plugin.current_page=0;



        }
        // the "constructor" method that gets called when the object is created
        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            plugin.settings.list_item=[];
            plugin.settings.total_show=3;

            var $list_product=$element.find('.list-product');
            plugin.settings.template_item=$list_product.find('>.slide.item:first').getOuterHTML();
            for(var i=0;i<$list_product.find('>.slide.item').length;i=i+plugin.settings.total_show){
                var chunks=[];
                for(var j=i;j<i+plugin.settings.total_show;j++){
                    var $item=$list_product.find('>.slide.item:eq('+j+')');
                    if($item.length>0) {
                        var html_item_template = $item.getOuterHTML();
                        chunks.push(html_item_template);
                    }
                }
                plugin.settings.list_item.push(chunks);
            }
            $list_product.find('>.slide.item').remove();
            var first_list_item=plugin.settings.list_item[0];
            for(var i=0;i<first_list_item.length;i++){
                var item=first_list_item[i];
                $(item).appendTo($list_product);
            }
            plugin.current_page=0;

            $element.find('>.control.next').click(function(){
                var list_item=plugin.settings.list_item[plugin.current_page+1];
                if(typeof list_item!="undefined"){
                    plugin.current_page++;
                    $list_product.find('>.slide.item').remove();
                    for(var i=0;i<list_item.length;i++){
                        var item=list_item[i];
                        $(item).appendTo($list_product);
                    }
                }

            });
            $element.find('>.control.pre').click(function(){
                var list_item=plugin.settings.list_item[plugin.current_page-1];
                if(typeof list_item!="undefined"){
                    plugin.current_page--;
                    $list_product.find('>.slide.item').remove();
                    for(var i=0;i<list_item.length;i++){
                        var item=list_item[i];
                        $(item).appendTo($list_product);
                    }
                }

            });



        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.vtvslider = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('vtvslider')) {
                var plugin = new $.vtvslider(this, options);

                $(this).data('vtvslider', plugin);

            }

        });

    }

})(jQuery);
