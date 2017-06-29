//huong dan su dung
/*
 $('.view_importproductvatgia_all').view_importproductvatgia_all();

 view_importproductvatgia_all=$('.view_importproductvatgia_all').data('view_importproductvatgia_all');
 console.log(view_importproductvatgia_all);
 */

// jQuery Plugin for SprFlat admin view_importproductvatgia_all
// Control options and basic function of view_importproductvatgia_all
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_importproductvatgia_all = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for view_importproductvatgia_all
            //be sure to be same as colors on main.css or custom-variables.less

        }

        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        plugin.settings = {}

        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element

        // the "constructor" method that gets called when the object is created
        plugin.add_product_to_database = function() {
            stop=plugin.settings.stop;
            if(stop==1){
                return;
            }
            var $vatgia_wrapper_product=$element.find('.vatgia-wrapper-product');
            var category_id=$element.find('input[name="hika_category_id"]').val();
            var vatgia_category_id=$element.find('input[name="vatgia_category_id[]"]').autoNumeric('get');
            var product={};
            product.product_name=$element.find('.product_name').html();
            product.product_price=$element.find('.product_price').html();
            product.price_promotion=$element.find('.price_promotion').html();
            product.price_promotion_time=$element.find('.price_promotion_time').html();
            product.product_keywords=$element.find('.product_keywords ').html();
            product.meta_description=$element.find('.meta_description').html();
            product.vendor_name=$element.find('.vendor_name').html();
            product.src_image=escape($element.find('.src_image').attr('src'));
            var product_description=$element.find('.vatgia-wrapper-product .content').html();
            if(typeof product_description=='undefined' || product_description.trim()==''){
                product_description="no thing";
            }
            product.product_description=$.base64Encode(product_description);
            product.category_id=category_id;

            plugin.settings.ajax_add_product_vatgia_to_database=$.ajax({
                type: "POST",
                url: 'index.php?themproduct=1',
                dataType: "json",
                data: (function () {

                    dataPost = {
                        option: 'com_hikashop',
                        ctrl:"product",
                        task: 'jsonp_add_product_vatgia_to_database',
                        product: product

                    };
                    return dataPost;
                })(),
                beforeSend: function () {
                },
                error: function(){
                },
                success: function (response) {
                    plugin.get_detail_product_from_vatgia();
                }
            });

        }
        plugin.show_product = function(response) {
            $element.find('.product_name').html(response.product_name);
            $element.find('.product_price').html(response.product_price);
            $element.find('.price_promotion').html(response.price_promotion);
            $element.find('.price_promotion_time').html(response.price_promotion_time);
            $element.find('.vendor_name').html(response.vendor_name);
            $element.find('.meta_description').html($.base64Decode(response.meta_description));
            $element.find('.product_keywords ').html($.base64Decode(response.product_keywords) );
            $element.find('.src_image').attr('src', $.base64Decode(response.src_image));
            var $vatgia_wrapper_product=$element.find('.vatgia-wrapper-product');
            $vatgia_wrapper_product.empty();
            var html=$.base64Decode(response.html_content);
            $vatgia_wrapper_product.html(html);
            plugin.add_product_to_database();
        }
        plugin.init = function() {
            $element.find('button.get_product').prop('disabled',false);
            $element.find('button.importproductvatgia').prop('disabled',false);
            $element.find('input[name="vatgia_category_id[]"]').autoNumeric('init',{
                mDec: 0,
                aSep: ' ',
                aSign: ''
            });

            plugin.settings.current_vatgia_category_id=$element.find('input[name="vatgia_category_id[]"]').autoNumeric('get');
            $element.find('input[name="vatgia_category_id[]"]').change(function(){
                current_vatgia_category_id=plugin.settings.current_vatgia_category_id;
                if(current_vatgia_category_id!=0){
                    if (confirm('Are you sure you want to change vatgia category_id?')) {
                        // Save it!
                    } else {
                        $(this).val(current_vatgia_category_id);
                    }
                    plugin.settings.current_vatgia_category_id=$(this).autoNumeric('get');

                }
            });
            $element.find('.cancel_importproductvatgia').click(function(){
                plugin.settings.stop=1;
                plugin.settings.list_system_category_id_and_vatgia_category_id=[];
                plugin.settings.ajax_import_product.abort();

                $element.find('button.get_product').prop('disabled',false);
                $element.find('button.importproductvatgia').prop('disabled',false);
            });

            $element.find('.get_product:not([disabled])').click(function(){
                /*alert('you cannot import again');
                return false;*/
                plugin.getproductsvatgia();
            });
            $element.find('.importproductvatgia:not([disabled])').click(function(){
                plugin.get_detail_product_from_vatgia();
            });
        }

        plugin.get_detail_product_from_vatgia = function(link_product) {
            stop=plugin.settings.stop;
            if(stop==1){
                return;
            }
            $element.find('button.get_product').prop('disabled',true);
            $element.find('button.importproductvatgia').prop('disabled',true);
            $vatgia_deal=$element.find('input[name="vatgia_deal"]');
            vatgia_deal=0;
            if($vatgia_deal.is(":checked")){
                var vatgia_deal=$vatgia_deal.val();
            }

            var vatgia_category_id=$element.find('input[name="vatgia_category_id[]"]').autoNumeric('get');


            list_list=plugin.settings.list_list;

            if(list_list.length==0){
                $element.find('.vatgia-import-product-div-loading').html('imported product completed');
                $element.find('button.get_product').prop('disabled',false);
                $element.find('button.importproductvatgia').prop('disabled',false);
                plugin.get_products_by_per_category();
            }
            if(typeof link_product==="undefined")
            {
                var link_product=list_list.pop();
            }

            var category_id=$element.find('input[name="hika_category_id"]').val();


            if(vatgia_category_id==''||!$.isNumeric(vatgia_category_id))
            {
                alert('please input category vatgia');
                return false;
            }
            plugin.settings.ajax_import_product= $.ajax({
                type: "POST",
                url: 'index.php?get_detail_product_from_vatgia=1',
                dataType: "json",
                data: (function () {

                    dataPost = {
                        option: 'com_hikashop',
                        ctrl:"category",
                        task: 'get_detail_product_from_vatgia',
                        vatgia_deal: vatgia_deal,
                        vatgia_category_id: vatgia_category_id,
                        category_id: category_id,
                        link_product: link_product,

                    };
                    return dataPost;
                })(),
                beforeSend: function () {
                    $element.find('.vatgia-import-product-div-loading').html('<b>importing product</b>');
                },
                error: function(){
                    $element.find('.vatgia-import-product-div-loading').html('<b>error import product</b>');
                },
                success: function (response) {

                    if(response.reload_product==1){
                        plugin.get_detail_product_from_vatgia(response.link_product);
                    }else{
                        $element.find('.vatgia-import-product-div-loading').html('imported product completed');
                        plugin.show_product(response);
                    }
                }
            });

        }
        plugin.get_products_by_per_category = function() {
            stop=plugin.settings.stop;
            if(stop==1){
                return;
            }

            list_system_category_id_and_vatgia_category_id=plugin.settings.list_system_category_id_and_vatgia_category_id;
            if(list_system_category_id_and_vatgia_category_id.length>0){
                var system_category_id_and_vatgia_category_id=list_system_category_id_and_vatgia_category_id.pop();
                var vatgia_category_id=system_category_id_and_vatgia_category_id.vatgia_category_id;
                var category_id=system_category_id_and_vatgia_category_id.category_id;
                var filter_page_number=system_category_id_and_vatgia_category_id.filter_page_number;
                var filter_by=system_category_id_and_vatgia_category_id.filter_by;
                var vatgia_deal=system_category_id_and_vatgia_category_id.vatgia_deal;
                $element.find('input[name="hika_category_id"]').val(category_id);
                if(vatgia_category_id!=0){
                    $.ajax({
                        type: "POST",
                        url: 'index.php?getproductsvatgia',
                        dataType: "json",
                        data: (function () {

                            dataPost = {
                                option: 'com_hikashop',
                                ctrl:"category",
                                task: 'getproductsvatgia',
                                filter_page_number: filter_page_number,
                                filter_by: filter_by,
                                vatgia_deal: vatgia_deal,
                                vatgia_category_id: vatgia_category_id

                            };
                            return dataPost;
                        })(),
                        beforeSend: function () {
                        },
                        error: function(){
                        },
                        success: function (response) {
                            $element.find('.link').html(response.link);
                            $element.find('.vatgia-wrapper').html($.base64Decode(response.html));
                            $element.find('.vatgia-wrapper div.no_picture_thumb').removeAttr('onmouseover');

                            $wrapper=$element.find('.vatgia-wrapper .wrapper');
                            var list_list=[];
                            for(var i=0;i<$wrapper.length;i++){
                                var $item=$($wrapper.get(i));
                                var link=$item.find('.name a').attr('href');
                                list_list.push(link);
                            }
                            plugin.settings.list_list=list_list;

                        }
                    });


                }else{
                    plugin.get_products_by_per_category();
                }
            }
        }
        plugin.getproductsvatgia = function() {
            plugin.settings.stop=0;
            $list_item_category=$element.find('tr.item-category');
            var list_system_category_id_and_vatgia_category_id=[];
            for(var i=0;i<$list_item_category.length;i++){
                $item_category=$($list_item_category.get(i));
                var system_category_id_and_vatgia_category_id={};

                //selected
                var $selected=$item_category.find('input[name="selected[]"]');
                if(!$selected.is(':checked')){
                    continue;
                }


                //category_id
                var category_id=$item_category.find('input[name="category_id[]"]').val();
                system_category_id_and_vatgia_category_id.category_id=category_id;

                //page number
                var filter_page_number=$item_category.find('select[name="filter_page_number[]"]').val();
                system_category_id_and_vatgia_category_id.filter_page_number=filter_page_number;

                //filter by
                var filter_by=$item_category.find('select[name="filter_by[]"]').val();
                system_category_id_and_vatgia_category_id.filter_by=filter_by;

                //filter by
                var $vatgia_deal=$item_category.find('select[name="vatgia_deal[]"]');
                if($vatgia_deal.is(':checked')){
                    system_category_id_and_vatgia_category_id.vatgia_deal=$vatgia_deal.val();
                }else{
                    system_category_id_and_vatgia_category_id.vatgia_deal=0;
                }



                var $vatgia_category_id=$item_category.find('input[name="vatgia_category_id[]"]');
                var vatgia_category_id=$($vatgia_category_id).autoNumeric('get');
                system_category_id_and_vatgia_category_id.vatgia_category_id=vatgia_category_id;



                list_system_category_id_and_vatgia_category_id.push(system_category_id_and_vatgia_category_id);
            }

            plugin.settings.list_system_category_id_and_vatgia_category_id=list_system_category_id_and_vatgia_category_id;
            plugin.get_products_by_per_category();

        }
        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_importproductvatgia_all = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_importproductvatgia_all')) {
                var plugin = new $.view_importproductvatgia_all(this, options);

                $(this).data('view_importproductvatgia_all', plugin);

            }

        });

    }

})(jQuery);
