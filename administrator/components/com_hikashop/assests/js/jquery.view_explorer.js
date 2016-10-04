//huong dan su dung
/*
 $('.view_explorer').view_explorer();

 view_explorer=$('.view_explorer').data('view_explorer');
 console.log(view_explorer);
 */

// jQuery Plugin for SprFlat admin view_explorer
// Control options and basic function of view_explorer
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_explorer = function(element, options) {

        // plugin's default options
        var defaults = {
            filter_id:0
            //main color scheme for view_explorer
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
                var category_id=$li.data('category_id');
                var $ul=$li.find('>ul');

                if($ul.is(":visible")) {

                    $(this).switchClass("icon-folder-minus", "icon-folder-plus", 1000, "easeInOutQuad");
                    var list_cookie_category_store=$.cookie('list_cookie_category_store');
                    list_cookie_category_store = JSON.parse(list_cookie_category_store);
                    var index_category = list_cookie_category_store.indexOf(category_id);

                    if(typeof list_cookie_category_store !="undefined" && list_cookie_category_store!="" && typeof index_category !="undefined")
                    {
                        list_cookie_category_store.splice(index_category, 1);
                    }
                    list_cookie_category_store=JSON.stringify(list_cookie_category_store);
                    $.cookie('list_cookie_category_store',list_cookie_category_store);
                    $ul.hide();
                }else{
                    $(this).switchClass("icon-folder-plus","icon-folder-minus", 1000, "easeInOutQuad");
                    var list_cookie_category_store=$.cookie('list_cookie_category_store');
                    if(typeof list_cookie_category_store=="undefined" || list_cookie_category_store=="" ){
                        list_cookie_category_store=JSON.stringify([]);
                    }
                    console.log(list_cookie_category_store);
                    list_cookie_category_store = JSON.parse(list_cookie_category_store);
                    var index_category = list_cookie_category_store.indexOf(category_id);
                    if (typeof index_category == "undefined" || index_category==-1) {
                        list_cookie_category_store.push(category_id);
                    }
                    list_cookie_category_store=JSON.stringify(list_cookie_category_store);
                    $.cookie('list_cookie_category_store',list_cookie_category_store);
                    $ul.show();
                }


            });


            $element.find('li').each(function(){
                var total_product=0;
                var $item_li=$(this);
                $item_li.find('li').each(function(){
                    var current_total_product1=$(this).data('total_product');
                    total_product+=current_total_product1;
                });
                var current_total_product=$item_li.data('total_product');
                total_product+=current_total_product;
                $item_li.find('a >.sub-total-product').html('sub:'+(total_product-current_total_product));
                $item_li.find('a >.all-total-product').html('all:'+total_product);

            });
            var list_cookie_category_store=$.cookie('list_cookie_category_store');
            if(typeof list_cookie_category_store=="" ){
                list_cookie_category_store=JSON.stringify([]);
            }
            list_cookie_category_store=JSON.parse(list_cookie_category_store);
            list_cookie_category_store.forEach(function(category_id) {
                $element.find('span[data-category_id="'+category_id+'"]').trigger('click');
            });
            var filter_id=plugin.settings.filter_id;
            console.log(filter_id);
            $element.find('li[data-category_id="'+filter_id+'"]').addClass('selected');


        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_explorer = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_explorer')) {
                var plugin = new $.view_explorer(this, options);

                $(this).data('view_explorer', plugin);

            }

        });

    }

})(jQuery);
