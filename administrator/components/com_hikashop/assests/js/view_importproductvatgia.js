//huong dan su dung
/*
 $('.view_importproductvatgia').view_importproductvatgia();

 view_importproductvatgia=$('.view_importproductvatgia').data('view_importproductvatgia');
 console.log(view_importproductvatgia);
 */

// jQuery Plugin for SprFlat admin view_importproductvatgia
// Control options and basic function of view_importproductvatgia
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_importproductvatgia = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for view_importproductvatgia
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
            var $vatgia_wrapper_product=$element.find('.vatgia-wrapper-product');
            var category_id=$element.find('input[name="hika_category_id"]').val();
            var vatgia_category_id=$element.find('input[name="vatgia_category_id"]').autoNumeric('get');
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
                product_description="";
            }
            product.product_description=$.base64Encode(product_description);
            product.category_id=category_id;

            $.ajax({
                type: "POST",
                url: 'index.php?themproduct=1',
                dataType: "json",
                data: (function () {

                    dataPost = {
                        option: 'com_hikashop',
                        ctrl:"product",
                        task: 'add_product_vatgia_to_database',
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
            console.log('show_product');
            $element.find('.product_name').html(response.product_name);
            $element.find('.product_price').html(response.product_price);
            $element.find('.price_promotion').html(response.price_promotion);
            $element.find('.price_promotion_time').html(response.price_promotion_time);
            $element.find('.vendor_name').html(response.vendor_name);
            $element.find('.meta_description').html($.base64Decode(response.meta_description));
            $element.find('.product_keywords ').html($.base64Decode(response.product_keywords) );
            $element.find('.src_image').attr('src', $.base64Decode(response.src_image));
            var $vatgia_wrapper_product=$element.find('.vatgia-wrapper-product');
            var html=$.base64Decode(response.html_content);
            $vatgia_wrapper_product.html(html);
            plugin.add_product_to_database();
        }
        plugin.init = function() {
            $element.find('button.get_product').prop('disabled',false);
            $element.find('button.importproductvatgia').prop('disabled',false);
            $element.find('input[name="vatgia_category_id"]').autoNumeric('init',{
                mDec: 0,
                aSep: ' ',
                aSign: ''
            });

            plugin.settings.current_vatgia_category_id=$element.find('input[name="vatgia_category_id"]').autoNumeric('get');
            $element.find('input[name="vatgia_category_id"]').change(function(){
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
                plugin.settings.ajax_import_product.abort();
                $element.find('button.get_product').prop('disabled',false);
                $element.find('button.importproductvatgia').prop('disabled',false);
            });

            $element.find('.get_product:not([disabled])').click(function(){
                /*alert('you cannot import again');
                return false;*/
                plugin.getproductvatgia();
            });
            $element.find('.importproductvatgia:not([disabled])').click(function(){
                /*alert('you cannot import again');
                return false;*/
                $wrapper=$element.find('.wrapper');
                var list_list=[];
                for(var i=0;i<$wrapper.length;i++){
                    var $item=$($wrapper.get(i));
                    var link=$item.find('.name a').attr('href');
                    list_list.push(link);
                }
                plugin.settings.list_list=list_list;
                plugin.get_detail_product_from_vatgia();
            });
        }

        plugin.get_detail_product_from_vatgia = function(link_product) {

            $element.find('button.get_product').prop('disabled',true);
            $element.find('button.importproductvatgia').prop('disabled',true);
            $vatgia_deal=$element.find('input[name="vatgia_deal"]');
            vatgia_deal=0;
            if($vatgia_deal.is(":checked")){
                var vatgia_deal=$vatgia_deal.val();
            }

            var vatgia_category_id=$element.find('input[name="vatgia_category_id"]').autoNumeric('get');
            var list_list=plugin.settings.list_list;
            if(list_list.length==0){
                $element.find('.vatgia-import-product-div-loading').html('imported product completed');
                $element.find('button.get_product').prop('disabled',false);
                $element.find('button.importproductvatgia').prop('disabled',false);
                return;
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
        plugin.getproductvatgia = function() {
            var vatgia_category_id=$element.find('input[name="vatgia_category_id"]').autoNumeric('get');
            var category_id=$element.find('input[name="hika_category_id"]').val();

            var filter_by=$element.find('select[name="filter_by"]').val();
            var filter_page_number=$element.find('select[name="filter_page_number"]').val();
            if(vatgia_category_id==''||!$.isNumeric(vatgia_category_id))
            {
                alert('please input category vatgia');
                return false;
            }
            $.ajax({
                type: "GET",
                url: 'index.php',
                dataType: "json",
                data: (function () {

                    dataPost = {
                        option: 'com_hikashop',
                        ctrl:"category",
                        task: 'getproductsvatgia',
                        vatgia_category_id: vatgia_category_id,
                        category_id: category_id,
                        filter_page_number: filter_page_number,
                        filter_by: filter_by

                    };
                    return dataPost;
                })(),
                beforeSend: function () {
                    $element.find('.link').html("http://www.vatgia.com/"+vatgia_category_id+",hot/abc.html");
                    $element.find('.vatgia-div-loading').html('loading');
                },
                error: function(){
                    $element.find('.vatgia-div-loading').html('<b>error load product please check</b>');
                },
                success: function (response) {

                    $element.find('.vatgia-div-loading').html('done');
                    $element.find('.link').html(response.link);
                    $element.find('.vatgia-wrapper').html($.base64Decode(response.html));
                    $element.find('.vatgia-wrapper div.no_picture_thumb').removeAttr('onmouseover');
                    //plugin.get_detail_product_from_vatgia();


                }
            });

        }
        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_importproductvatgia = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_importproductvatgia')) {
                var plugin = new $.view_importproductvatgia(this, options);

                $(this).data('view_importproductvatgia', plugin);

            }

        });

    }

})(jQuery);
