//huong dan su dung
/*
 $('.mod_tab_products').mod_tab_products();

 mod_tab_products=$('.mod_tab_products').data('mod_tab_products');
 console.log(mod_tab_products);
 */

// jQuery Plugin for SprFlat admin mod_tab_products
// Control options and basic function of mod_tab_products
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.mod_tab_products = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for mod_tab_products
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
            $.set_height($element.find('.item .title'));
            var style=plugin.settings.style;
            var module_id=plugin.settings.module_id;
            if(style=='slider'){
               /* $element.find('.product_slide').slick({

                    // normal options...
                    infinite: true,
                    fade: true,
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    // the magic
                });*/
                $element.find('.product_slide').vtvslider({

                });


            }

            $element.find('.sub-category').click(function(){
                var $wrapper_content =$(this).closest('.wrapper-content');
                var option_click= {
                    option: 'com_hikashop',
                    ctrl: 'product',
                    task: 'ajax_get_product_by_category_id_and_type'

                };
                option_click= $.param(option_click);
                var data_submit={};
                data_submit.category_id=$(this).data('category_id');
                data_submit.params=plugin.settings.params;
                var ajax_web_design=$.ajax({
                    contentType: 'application/json',
                    type: "POST",
                    dataType: "json",
                    url: root_ulr+'index.php?'+option_click,
                    data: JSON.stringify(data_submit),
                    beforeSend: function () {
                        $wrapper_content.find('.list-product').bho88loading();
                    },
                    success: function (response) {
                        $wrapper_content.find('.list-product').bho88loading(true);
                        if(response.e==0)
                        {
                            var list_product=response.r;
                            var $product_slide=$wrapper_content.find('.product_slide').data('vtvslider');
                            $product_slide.update_product(list_product);

                        }else if(response.e==1){
                            alert(response.m);
                        }



                    }
                });



            });
            var params=plugin.settings.params;
            $element.find('.tab-product').zozoTabs({
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
                maxRows: params.maxRows||200
            });


        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.mod_tab_products = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('mod_tab_products')) {
                var plugin = new $.mod_tab_products(this, options);

                $(this).data('mod_tab_products', plugin);

            }

        });

    }

})(jQuery);
