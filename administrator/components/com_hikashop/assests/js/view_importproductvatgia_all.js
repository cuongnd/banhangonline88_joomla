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
            list_link_product:[],
            current_ajax: $.ajax(),
            list_vg_product:[]
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
            var stop=plugin.settings.stop;
            if(stop==1){
                return;
            }
            if(plugin.settings.current_ajax.state()=='pending'){
                console.log("you can not call(add_product_to_database) this ajax because it running");
                return false;

            }
            var current_system_category_id_and_vatgia_category_id=plugin.settings.current_system_category_id_and_vatgia_category_id;
            var $vatgia_wrapper_product=$element.find('.vatgia-wrapper-product');
            var category_id=current_system_category_id_and_vatgia_category_id.category_id;
            var vatgia_category_id=current_system_category_id_and_vatgia_category_id.vatgia_category_id;
            var product={};
            product.product_name=$element.find('.product_name').html();
            if(product.product_name==''){

            }
            console.log("product_name:"+product.product_name);
            product.product_price=$element.find('.product_price').html();
            product.vatgia_product_id=$element.find('.vatgia_product_id').html();
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

            plugin.settings.current_ajax=$.ajax({
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
                    
                    console.log(JSON.stringify(response));
                    plugin.get_detail_product_from_vatgia();
                }
            });

        }
        plugin.show_product = function(response) {
            $element.find('.product_name').html(response.product_name);
            $element.find('.vatgia_product_id').html(response.vatgia_product_id);
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
            plugin.settings = $.extend({}, defaults, options);
            $element.find('button.get_product').prop('disabled',false);
            $element.find('button.importproductvatgia').prop('disabled',false);
            $element.find('input[name="vatgia_category_id[]"]').autoNumeric('init',{
                mDec: 0,
                aSep: ' ',
                aSign: ''
            });
            plugin.settings.list_current_vatgia_category_id=[];
            var $list_tr_item_category=$element.find('.list-category-vat-gia-and-category-system tbody tr.item-category');
            for(var i=0;i<$list_tr_item_category.length;i++){
                var $tr=$($list_tr_item_category.get(i));
                var vatgia_category_id=$tr.find('input[name="vatgia_category_id[]"]').autoNumeric('get');
                plugin.settings.list_current_vatgia_category_id.push(vatgia_category_id);

            }

            $element.find('input[name="vatgia_category_id[]"]').change(function(){
                var $tr=$(this).closest('tr.item-category');
                var index=$tr.index();
                var current_vatgia_category_id=plugin.settings.list_current_vatgia_category_id[index];
                if(current_vatgia_category_id!=0){
                    if (confirm('Are you sure you want to change vatgia category_id?')) {
                    } else {
                        $(this).autoNumeric('set',current_vatgia_category_id);
                    }
                }
                plugin.settings.list_current_vatgia_category_id[index]=$(this).autoNumeric('get');
            });
            $element.find('.cancel_importproductvatgia').click(function(){
                plugin.settings.stop=1;
                plugin.settings.list_system_category_id_and_vatgia_category_id=[];
                plugin.settings.get_detail_product_from_vatgia.abort();

                $element.find('button.get_product').prop('disabled',false);
                $element.find('button.importproductvatgia').prop('disabled',false);
            });
            $element.find('select.total_page').change(function(){
                var total_page=$(this).val();
                var $tbody=$element.find('table.list-category-vat-gia-and-category-system tbody');
                $tbody.find('select[name="total_page[]"]').val(total_page).trigger("liszt:updated");
            });
            $element.find('select.filter_by').change(function(){
                var filter_by=$(this).val();
                var $tbody=$element.find('table.list-category-vat-gia-and-category-system tbody');
                $tbody.find('select[name="filter_by[]"]').val(filter_by).trigger("liszt:updated");
            });

            $element.find('.btn-test-get-content').click(function(){
                /*alert('you cannot import again');
                return false;*/
                var $item_category=$(this).closest('.item-category');
                plugin.test_get_products_by_per_category($item_category);
            });
            $element.find('.importproductvatgia:not([disabled])').click(function(){
                plugin.saveproductsvatgia();
            });
            $element.find('table.list-category-vat-gia-and-category-system thead .checkbox').click(function(){
                var $tbody=$element.find('table.list-category-vat-gia-and-category-system tbody');
                if($(this).is(':checked')){
                    $tbody.find('.item-category input[name="selected[]"]').prop('checked',true);
                }else{
                    $tbody.find('.item-category input[name="selected[]"]').prop('checked',false);
                }
            });
            $element.find('table.list-category-vat-gia-and-category-system tbody .item-category td:first-child').click(function(){
                var $selected=$(this).find('input[name="selected[]"]');
                $selected.prop('checked',!$selected.is(':checked')).trigger('click');
            });
            $element.find('table.list-category-vat-gia-and-category-system tbody .item-category input[name="selected[]"]').click(function(){
                var is_checked=true;
                var $list_tr=$element.find('table.list-category-vat-gia-and-category-system tbody .item-category');
                for(var i=0;i<$list_tr.length;i++){
                    var $tr=$($list_tr.get(i));
                    var $selected=$tr.find('input[name="selected[]"]');
                    if(!$selected.is(':checked')){
                        is_checked=false;
                        break;
                    }
                }
                var $thead_checked=$element.find('table.list-category-vat-gia-and-category-system thead .checkbox');
                if(is_checked)
                    $thead_checked.prop('checked',true);
                else
                    $thead_checked.prop('checked',false);

            });
        }

        plugin.get_detail_product_from_vatgia = function() {
            var stop=plugin.settings.stop;
            if(stop==1){
                return;
            }
            if(plugin.settings.current_ajax.state()=='pending'){
                console.log("you can not call(get_detail_product_from_vatgia) this ajax because it running");
                return;
            }
            $element.find('button.get_product').prop('disabled',true);
            $element.find('button.importproductvatgia').prop('disabled',true);
            var current_system_category_id_and_vatgia_category_id=plugin.settings.current_system_category_id_and_vatgia_category_id;

            var vatgia_category_id=current_system_category_id_and_vatgia_category_id.vatgia_category_id;
            var vatgia_deal=current_system_category_id_and_vatgia_category_id.vatgia_deal;


            var list_link_product=plugin.settings.list_link_product;
            console.log(list_link_product);
            if(list_link_product.length==0){
                $element.find('.vatgia-import-product-div-loading').html('imported product completed');
                $element.find('button.get_product').prop('disabled',false);
                $element.find('button.importproductvatgia').prop('disabled',false);
                plugin.save_products_by_per_category();
                return false;
            }
            var item_vg_link_product=list_link_product.pop();
            if(item_vg_link_product.link==null || item_vg_link_product.link.trim()==""){
                console.log('link_product is null');
                return false;
            }

            var category_id=current_system_category_id_and_vatgia_category_id.category_id;
            console.log('link_product: http://vatgia.com'+item_vg_link_product.link);
            // somewhere else...


            plugin.settings.current_ajax=$.ajax({
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
                        link_product: item_vg_link_product.link,
                        vg_product_id: item_vg_link_product.vg_p_id

                    };
                    return dataPost;
                })(),
                beforeSend: function () {
                    $element.find('.vatgia-import-product-div-loading').html('<b>importing product</b>');
                },
                error: function(){
                    $element.find('.vatgia-import-product-div-loading').html('<b>error import product</b>');
                },
                success: function (response,jqxhr) {
                    
                    if(response.reload_product==1){
                        console.log('there exist sub product');
                        console.log('response:');
                        console.log(JSON.stringify(response));
                        var list_link_product=plugin.settings.list_link_product;
                        var list_vg_product=plugin.settings.list_vg_product;

                        var response_list_link_product=response.list_link_product;
                        for(var i=0;i<response_list_link_product.length;i++){
                            var item_vg_link=response_list_link_product[i];
                            if(item_vg_link.link!="")
                            {
                                var plag_exists=0;
                                for(var j=0;j<list_vg_product.length;j++){
                                    var item_vg_product=list_vg_product[j];
                                    if(item_vg_link.vatgia_product_id==item_vg_product.vg_id){
                                        console.log('exists product system  link: '+item_vg_product.link);
                                        plag_exists=1;
                                        break;
                                    }
                                }
                                if(plag_exists==1){
                                    continue;
                                }
                                list_link_product.push(item_vg_link);
                            }
                        }
                        plugin.settings.list_link_product=list_link_product;
                        plugin.get_detail_product_from_vatgia();
                    }else{
                        console.log('show product from response');
                        $element.find('.vatgia-import-product-div-loading').html('imported product completed');
                        plugin.show_product(response);
                    }
                }
            });

        }
        plugin.test_get_products_by_per_category = function($item_category) {
            //category_id
            var category_id=$item_category.find('input[name="category_id[]"]').val();

            //page number
            var filter_page_number=$item_category.find('select[name="filter_page_number[]"]').val();

            //filter by
            var filter_by=$item_category.find('select[name="filter_by[]"]').val();

            //filter by
            var $vatgia_deal=$item_category.find('select[name="vatgia_deal[]"]');
            var vatgia_deal=0;
            if($vatgia_deal.is(':checked')){
                var vatgia_deal=$vatgia_deal.val();
            }
            var $vatgia_category_id=$item_category.find('input[name="vatgia_category_id[]"]');
            var vatgia_category_id=$($vatgia_category_id).autoNumeric('get');
            vatgia_category_id=vatgia_category_id;


            $.ajax({
                type: "POST",
                url: 'index.php?getproductsvatgia',
                dataType: "json",
                data: (function () {

                    var dataPost = {
                        option: 'com_hikashop',
                        ctrl:"category",
                        task: 'getproductsvatgia',
                        filter_page_number: filter_page_number,
                        filter_by: filter_by,
                        vatgia_deal: vatgia_deal,
                        category_id: category_id,
                        vatgia_category_id: vatgia_category_id

                    };
                    return dataPost;
                })(),
                beforeSend: function () {
                    $('.div-loading').show();
                },
                error: function(){
                },
                success: function (response) {
                    $('.div-loading').hide();
                    console.log(response.link);
                    var $test_get_content_product_vt=$('#test_get_content_product_vt');
                    $test_get_content_product_vt.find('.modal-vatgia-wrapper').html($.base64Decode(response.html));
                    $test_get_content_product_vt.find('.modal-vatgia-wrapper div.no_picture_thumb').removeAttr('onmouseover');
                    $test_get_content_product_vt.modal('show')
                }
            });

        }
        plugin.save_products_by_per_category = function() {
            console.log('get_products_by_per_category');
            var stop=plugin.settings.stop;
            if(stop==1){
                return;
            }
            if(plugin.settings.current_ajax.state()=='pending'){
               console.log("you can not call(save_products_by_per_category) this ajax because it running");
                return false;
            }
            
            var func_ajax_getproductsvatgia=function(system_category_id_and_vatgia_category_id) {
                var tr_index = system_category_id_and_vatgia_category_id.index;
                $($list_tr_item_category.get(tr_index)).find('td .state').html('processing');
                var filter_page_number=system_category_id_and_vatgia_category_id.filter_page_number;
                system_category_id_and_vatgia_category_id.filter_page_number++;
                plugin.settings.current_system_category_id_and_vatgia_category_id = system_category_id_and_vatgia_category_id;
                var vatgia_category_id = system_category_id_and_vatgia_category_id.vatgia_category_id;
                var category_id = system_category_id_and_vatgia_category_id.category_id;
                var filter_by = system_category_id_and_vatgia_category_id.filter_by;
                var vatgia_deal = system_category_id_and_vatgia_category_id.vatgia_deal;
                console.log("http://www.vatgia.com/"+vatgia_category_id+","+filter_by+"/abc.html?&page="+filter_page_number);

                if(plugin.settings.current_ajax.state()=='pending'){
                    console.log("you can not call(func_ajax_getproductsvatgia ) this ajax because it running");
                    return false;
                }
                plugin.settings.current_ajax=$.ajax({
                    type: "POST",
                    url: 'index.php?getproductsvatgia',
                    dataType: "json",
                    data: (function () {

                        dataPost = {
                            option: 'com_hikashop',
                            ctrl: "category",
                            task: 'getproductsvatgia',
                            filter_page_number: filter_page_number,
                            filter_by: filter_by,
                            vatgia_deal: vatgia_deal,
                            category_id: category_id,
                            vatgia_category_id: vatgia_category_id

                        };
                        return dataPost;
                    })(),
                    beforeSend: function () {
                    },
                    error: function () {
                    },
                    success: function (response) {
                        
                        $element.find('.link').html(response.link);
                        $element.find('.vatgia-wrapper').html($.base64Decode(response.html));
                        $element.find('.vatgia-wrapper div.no_picture_thumb').removeAttr('onmouseover');

                        var $wrapper = $element.find('.vatgia-wrapper .wrapper');
                        var list_link_product =  plugin.settings.list_link_product;
                        var list_vg_product=plugin.settings.list_vg_product;
                        for (var i = 0; i < $wrapper.length; i++) {
                            var new_item={};
                            var $item = $($wrapper.get(i));
                            var link = $item.find('.name a').attr('href');
                            var vg_p_id=$item.attr('idata');
                            vg_p_id=parseInt(vg_p_id);
                            var plag_exists=0;
                            for(var j=0;j<list_vg_product.length;j++){
                                var item_system_vg=list_vg_product[j];
                                if(item_system_vg.vg_id==vg_p_id){
                                    console.log('exists product system  link: '+link);
                                    plag_exists=1;
                                    break;
                                }
                            }
                            if(plag_exists==1){
                                continue;
                            }


                            new_item.link=link;
                            new_item.vg_p_id = vg_p_id;
                            list_link_product.push(new_item);
                        }
                        plugin.settings.list_link_product = list_link_product;
                        if (list_link_product.length > 0) {
                            plugin.get_detail_product_from_vatgia();
                        } else {
                            plugin.save_products_by_per_category();
                        }

                    }
                });
            }
            var $list_tr_item_category=$element.find('.list-category-vat-gia-and-category-system tbody tr.item-category');
            $list_tr_item_category.find('td .state').empty();
            var list_system_category_id_and_vatgia_category_id=plugin.settings.list_system_category_id_and_vatgia_category_id;
            if(typeof plugin.settings.current_system_category_id_and_vatgia_category_id !="undefined"){
                var current_system_category_id_and_vatgia_category_id=plugin.settings.current_system_category_id_and_vatgia_category_id;
            }else if(list_system_category_id_and_vatgia_category_id.length>0){
                current_system_category_id_and_vatgia_category_id=list_system_category_id_and_vatgia_category_id.pop();
                console.log(current_system_category_id_and_vatgia_category_id);
            }else{
                plugin.settings.stop=1;
                alert('import completed !');
            }
            var system_category_id_and_vatgia_category_id={};
            if(current_system_category_id_and_vatgia_category_id.filter_page_number<=current_system_category_id_and_vatgia_category_id.total_page)
            {
                system_category_id_and_vatgia_category_id=current_system_category_id_and_vatgia_category_id;
                func_ajax_getproductsvatgia(system_category_id_and_vatgia_category_id);
            }else if(list_system_category_id_and_vatgia_category_id.length>0){
                system_category_id_and_vatgia_category_id=list_system_category_id_and_vatgia_category_id.pop();
                func_ajax_getproductsvatgia(system_category_id_and_vatgia_category_id);
            }else{
                plugin.settings.stop=1;
                alert('import completed !');
            }
        }
        plugin.saveproductsvatgia = function() {
            plugin.settings.stop=0;
            var $list_item_category=$element.find('tr.item-category');
            var list_system_category_id_and_vatgia_category_id=[];
            for(var i=0;i<$list_item_category.length;i++){
                var $item_category=$($list_item_category.get(i));
                var system_category_id_and_vatgia_category_id={};

                //selected
                var $selected=$item_category.find('input[name="selected[]"]');
                if(!$selected.is(':checked')){
                    continue;
                }
                var $vatgia_category_id=$item_category.find('input[name="vatgia_category_id[]"]');
                var vatgia_category_id=$($vatgia_category_id).autoNumeric('get');
                if(vatgia_category_id==0||vatgia_category_id==""){
                    continue
                }

                system_category_id_and_vatgia_category_id.index=i;
                system_category_id_and_vatgia_category_id.vatgia_category_id=vatgia_category_id;
                system_category_id_and_vatgia_category_id.filter_page_number=1;
                //category_id
                var category_id=$item_category.find('input[name="category_id[]"]').val();
                system_category_id_and_vatgia_category_id.category_id=category_id;

                //page number
                var total_page=$item_category.find('select[name="total_page[]"]').val();
                system_category_id_and_vatgia_category_id.total_page=parseInt(total_page);

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







                list_system_category_id_and_vatgia_category_id.push(system_category_id_and_vatgia_category_id);
            }
            if(list_system_category_id_and_vatgia_category_id.length==0){
                alert('there are no category selected or category vat gia null or is 0, please check agian');
            }else{
                plugin.settings.list_system_category_id_and_vatgia_category_id=list_system_category_id_and_vatgia_category_id;
                plugin.save_products_by_per_category();

            }

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
