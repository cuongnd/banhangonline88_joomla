//huong dan su dung
/*
 $('.view_importproducttaobao_all').view_importproducttaobao_all();

 view_importproducttaobao_all=$('.view_importproducttaobao_all').data('view_importproducttaobao_all');
 console.log(view_importproducttaobao_all);
 */

// jQuery Plugin for SprFlat admin view_importproducttaobao_all
// Control options and basic function of view_importproducttaobao_all
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_importproducttaobao_all = function(element, options) {

        // plugin's default options
        var defaults = {
            list_link_product:[],
            current_ajax: $.ajax(),
            list_tb_product:[]
            //main color scheme for view_importproducttaobao_all
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
            var current_system_category_id_and_taobao_category_id=plugin.settings.current_system_category_id_and_taobao_category_id;
            var $taobao_wrapper_product=$element.find('.taobao-wrapper-product');
            var category_id=current_system_category_id_and_taobao_category_id.category_id;
            var taobao_category_id=current_system_category_id_and_taobao_category_id.taobao_category_id;
            var product={};
            product.product_name=$element.find('.product_name').html();
            product.product_link=$element.find('.product_link').html();
            if(product.product_name==''){

            }
            console.log("product_name:"+product.product_name);
            product.product_price=$element.find('.product_price').html();
            product.taobao_product_id=$element.find('.taobao_product_id').html();
            product.price_promotion=$element.find('.price_promotion').html();
            product.price_promotion_time=$element.find('.price_promotion_time').html();
            product.product_keywords=$element.find('.product_keywords ').html();
            product.meta_description=$element.find('.meta_description').html();
            product.vendor_name=$element.find('.vendor_name').html();
            product.src_image=escape($element.find('.src_image').attr('src'));
            product.list_image=$element.find('div.list_image').data('list_image');
            var product_description=$element.find('.taobao-wrapper-product .content').html();
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
                        task: 'jsonp_add_product_taobao_to_database',
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
                    plugin.get_detail_product_from_taobao();
                }
            });

        }
        plugin.show_product = function(response) {
            $element.find('.product_name').html(response.product_name);
            $element.find('.taobao_product_id').html(response.taobao_product_id);
            $element.find('.product_link').html(response.product_link);
            $element.find('.product_price').html(response.product_price);
            $element.find('.price_promotion').html(response.price_promotion);
            $element.find('.price_promotion_time').html(response.price_promotion_time);
            $element.find('.vendor_name').html(response.vendor_name);
            $element.find('.meta_description').html($.base64Decode(response.meta_description));
            $element.find('.product_keywords ').html($.base64Decode(response.product_keywords) );

            $element.find('.src_image').attr('src', $.base64Decode(response.src_image));
            var list_image=response.list_image;
            var $list_image= $element.find('div.list_image');
            $list_image.data('list_image',list_image);
            $list_image.empty();
            for(var i=0;i<list_image.length;i++){
                var $image=$('<image style="width: 100px;height: 100px;float: left; margin: 10px" src="'+list_image[i]+'"/>');
                $image.appendTo($list_image);
            }
            var $taobao_wrapper_product=$element.find('.taobao-wrapper-product');
            $taobao_wrapper_product.empty();
            var html=$.base64Decode(response.html_content);
            $taobao_wrapper_product.html(html);
            plugin.add_product_to_database();
        }
        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            $element.find('button.get_product').prop('disabled',false);
            $element.find('button.importproducttaobao').prop('disabled',false);
            $element.find('input[name="taobao_category_id[]"]').autoNumeric('init',{
                mDec: 0,
                aSep: ' ',
                aSign: ''
            });
            plugin.settings.list_current_taobao_category_id=[];
            var $list_tr_item_category=$element.find('.list-category-vat-gia-and-category-system tbody tr.item-category');
            for(var i=0;i<$list_tr_item_category.length;i++){
                var $tr=$($list_tr_item_category.get(i));
                var taobao_category_id=$tr.find('input[name="taobao_category_id[]"]').autoNumeric('get');
                plugin.settings.list_current_taobao_category_id.push(taobao_category_id);

            }

            $element.find('input[name="taobao_category_id[]"]').change(function(){
                var $tr=$(this).closest('tr.item-category');
                var index=$tr.index();
                var current_taobao_category_id=plugin.settings.list_current_taobao_category_id[index];
                if(current_taobao_category_id!=0){
                    if (confirm('Are you sure you want to change tao bao category_id?')) {

                    } else {
                        $(this).autoNumeric('set',current_taobao_category_id);
                    }
                    plugin.settings.list_current_taobao_category_id[index]=$(this).autoNumeric('get');
                }

            });
            $element.find('.cancel_importproducttaobao').click(function(){
                plugin.settings.stop=1;
                plugin.settings.list_system_category_id_and_taobao_category_id=[];
                plugin.settings.get_detail_product_from_taobao.abort();

                $element.find('button.get_product').prop('disabled',false);
                $element.find('button.importproducttaobao').prop('disabled',false);
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
            $element.find('.importproducttaobao:not([disabled])').click(function(){
                plugin.saveproductstaobao();
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

        plugin.get_detail_product_from_taobao = function() {
            var stop=plugin.settings.stop;
            if(stop==1){
                return;
            }
            if(plugin.settings.current_ajax.state()=='pending'){
                console.log("you can not call(get_detail_product_from_taobao) this ajax because it running");
                return;
            }
            $element.find('button.get_product').prop('disabled',true);
            $element.find('button.importproducttaobao').prop('disabled',true);
            var current_system_category_id_and_taobao_category_id=plugin.settings.current_system_category_id_and_taobao_category_id;

            var taobao_category_id=current_system_category_id_and_taobao_category_id.taobao_category_id;
            var taobao_deal=current_system_category_id_and_taobao_category_id.taobao_deal;


            var list_link_product=plugin.settings.list_link_product;
            console.log(list_link_product);
            if(list_link_product.length==0){
                $element.find('.taobao-import-product-div-loading').html('imported product completed');
                $element.find('button.get_product').prop('disabled',false);
                $element.find('button.importproducttaobao').prop('disabled',false);
                plugin.save_products_by_per_category();
                return false;
            }
            var item_tb_link_product=list_link_product.pop();
            if(item_tb_link_product.link==null || item_tb_link_product.link.trim()==""){
                console.log('link_product is null');
                return false;
            }

            var category_id=current_system_category_id_and_taobao_category_id.category_id;
            var taobao_link_category=current_system_category_id_and_taobao_category_id.taobao_link_category;
            console.log(taobao_link_category);
            // somewhere else...


            plugin.settings.current_ajax=$.ajax({
                type: "POST",
                url: 'index.php?get_detail_product_from_taobao=1',
                dataType: "json",
                data: (function () {

                    dataPost = {
                        option: 'com_hikashop',
                        ctrl:"category",
                        task: 'get_detail_product_from_taobao',
                        taobao_deal: taobao_deal,
                        taobao_category_id: taobao_category_id,
                        category_id: category_id,
                        link_product: item_tb_link_product.link,
                        tb_product_id: item_tb_link_product.tb_p_id

                    };
                    return dataPost;
                })(),
                beforeSend: function () {
                    $element.find('.taobao-import-product-div-loading').html('<b>importing product</b>');
                },
                error: function(){
                    $element.find('.taobao-import-product-div-loading').html('<b>error import product</b>');
                },
                success: function (response,jqxhr) {
                    if(response.e==1){
                        console.log('Error:'+response.m);
                        plugin.get_detail_product_from_taobao();
                        return false;
                    }
                    if(response.reload_product==1){
                        console.log('there exist sub product');
                        console.log('response:');
                        console.log(JSON.stringify(response));
                        var list_link_product=plugin.settings.list_link_product;
                        var list_tb_product=plugin.settings.list_tb_product;

                        var response_list_link_product=response.list_link_product;
                        for(var i=0;i<response_list_link_product.length;i++){
                            var item_tb_link=response_list_link_product[i];
                            if(item_tb_link.link!="")
                            {
                                var plag_exists=0;
                                for(var j=0;j<list_tb_product.length;j++){
                                    var item_tb_product=list_tb_product[j];
                                    if(item_tb_link.taobao_product_id==item_tb_product.tb_id){
                                        console.log('exists product system  link: '+item_tb_product.link);
                                        plag_exists=1;
                                        break;
                                    }
                                }
                                if(plag_exists==1){
                                    continue;
                                }
                                list_link_product.push(item_tb_link);
                            }
                        }
                        plugin.settings.list_link_product=list_link_product;
                        plugin.get_detail_product_from_taobao();
                    }else{
                        console.log('show product from response');
                        response.product_link=item_tb_link_product.link;
                        $element.find('.taobao-import-product-div-loading').html('imported product completed');
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
            var $taobao_deal=$item_category.find('select[name="taobao_deal[]"]');
            var taobao_deal=0;
            if($taobao_deal.is(':checked')){
                var taobao_deal=$taobao_deal.val();
            }
            var $taobao_category_id=$item_category.find('input[name="taobao_category_id[]"]');
            var $taobao_category_name=$item_category.find('input[name="taobao_category_name[]"]');
            var $taobao_link_category=$item_category.find('input[name="taobao_link_category[]"]');
            var taobao_category_id=$($taobao_category_id).autoNumeric('get');
            taobao_category_id=taobao_category_id;


            $.ajax({
                type: "POST",
                url: 'index.php?getproductstaobao',
                dataType: "json",
                data: (function () {

                    var dataPost = {
                        option: 'com_hikashop',
                        ctrl:"category",
                        task: 'getproductstaobao',
                        filter_page_number: filter_page_number,
                        taobao_category_name: $taobao_category_name.val().trim(),
                        taobao_link_category: $taobao_link_category.val().trim(),
                        filter_by: filter_by,
                        taobao_deal: taobao_deal,
                        category_id: category_id,
                        taobao_category_id: taobao_category_id

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
                    $test_get_content_product_vt.find('.modal-taobao-wrapper').html($.base64Decode(response.html));
                    $test_get_content_product_vt.find('.modal-taobao-wrapper div.no_picture_thumb').removeAttr('onmouseover');
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

            var func_ajax_getproductstaobao=function(system_category_id_and_taobao_category_id) {
                var tr_index = system_category_id_and_taobao_category_id.index;
                $($list_tr_item_category.get(tr_index)).find('td .state').html('processing');
                var filter_page_number=system_category_id_and_taobao_category_id.filter_page_number;
                system_category_id_and_taobao_category_id.filter_page_number++;
                plugin.settings.current_system_category_id_and_taobao_category_id = system_category_id_and_taobao_category_id;
                var taobao_category_id = system_category_id_and_taobao_category_id.taobao_category_id;
                var category_id = system_category_id_and_taobao_category_id.category_id;
                var filter_by = system_category_id_and_taobao_category_id.filter_by;
                var taobao_deal = system_category_id_and_taobao_category_id.taobao_deal;
                var taobao_link_category = system_category_id_and_taobao_category_id.taobao_link_category;
                console.log(taobao_link_category);

                if(plugin.settings.current_ajax.state()=='pending'){
                    console.log("you can not call(func_ajax_getproductstaobao ) this ajax because it running");
                    return false;
                }
                plugin.settings.current_ajax=$.ajax({
                    type: "POST",
                    url: 'index.php?getproductstaobao',
                    dataType: "json",
                    data: (function () {

                        dataPost = {
                            option: 'com_hikashop',
                            ctrl: "category",
                            task: 'getproductstaobao',
                            filter_page_number: filter_page_number,
                            filter_by: filter_by,
                            taobao_deal: taobao_deal,
                            taobao_link_category: taobao_link_category,
                            category_id: category_id,
                            taobao_category_id: taobao_category_id

                        };
                        return dataPost;
                    })(),
                    beforeSend: function () {
                    },
                    error: function () {
                    },
                    success: function (response) {

                        $element.find('.link').html(response.link);
                        $element.find('.taobao-wrapper').html($.base64Decode(response.html));
                        var list_product=response.list_product;
                        var $wrapper = $element.find('.taobao-wrapper .wrapper');
                        var list_link_product =  plugin.settings.list_link_product;
                        var list_tb_product=plugin.settings.list_tb_product;
                        for (var i = 0; i < list_product.length; i++) {
                            var new_item={};
                            var iem_product = list_product[i];
                            var link = iem_product.detail_url;
                            var tb_p_id=iem_product.nid;
                            var plag_exists=0;
                            for(var j=0;j<list_tb_product.length;j++){
                                var item_system_tb=list_tb_product[j];
                                if(item_system_tb.tb_id==tb_p_id){
                                    console.log('exists product system  link: '+link);
                                    plag_exists=1;
                                    break;
                                }
                            }
                            if(plag_exists==1){
                                continue;
                            }


                            new_item.link=link;
                            new_item.tb_p_id = tb_p_id;
                            list_link_product.push(new_item);
                        }
                        plugin.settings.list_link_product = list_link_product;
                        if (list_link_product.length > 0) {
                            plugin.get_detail_product_from_taobao();
                        } else {
                            plugin.save_products_by_per_category();
                        }

                    }
                });
            }
            var $list_tr_item_category=$element.find('.list-category-vat-gia-and-category-system tbody tr.item-category');
            $list_tr_item_category.find('td .state').empty();
            var list_system_category_id_and_taobao_category_id=plugin.settings.list_system_category_id_and_taobao_category_id;
            if(typeof plugin.settings.current_system_category_id_and_taobao_category_id !="undefined"){
                var current_system_category_id_and_taobao_category_id=plugin.settings.current_system_category_id_and_taobao_category_id;
            }else if(list_system_category_id_and_taobao_category_id.length>0){
                current_system_category_id_and_taobao_category_id=list_system_category_id_and_taobao_category_id.pop();
                console.log(current_system_category_id_and_taobao_category_id);
            }else{
                plugin.settings.stop=1;
                alert('import completed !');
            }
            var system_category_id_and_taobao_category_id={};
            if(current_system_category_id_and_taobao_category_id.filter_page_number<=current_system_category_id_and_taobao_category_id.total_page)
            {
                system_category_id_and_taobao_category_id=current_system_category_id_and_taobao_category_id;
                func_ajax_getproductstaobao(system_category_id_and_taobao_category_id);
            }else if(list_system_category_id_and_taobao_category_id.length>0){
                system_category_id_and_taobao_category_id=list_system_category_id_and_taobao_category_id.pop();
                func_ajax_getproductstaobao(system_category_id_and_taobao_category_id);
            }else{
                plugin.settings.stop=1;
                alert('import completed !');
            }
        }
        plugin.saveproductstaobao = function() {
            plugin.settings.stop=0;
            var $list_item_category=$element.find('tr.item-category');
            var list_system_category_id_and_taobao_category_id=[];
            for(var i=0;i<$list_item_category.length;i++){
                var $item_category=$($list_item_category.get(i));
                var system_category_id_and_taobao_category_id={};

                //selected
                var $selected=$item_category.find('input[name="selected[]"]');
                if(!$selected.is(':checked')){
                    continue;
                }
                var $taobao_link_category=$item_category.find('input[name="taobao_link_category[]"]');
                var taobao_link_category=$taobao_link_category.val().trim();
                if(taobao_link_category==""){
                    continue
                }

                var $taobao_category_id=$item_category.find('input[name="taobao_category_id[]"]');
                var taobao_category_id=$($taobao_category_id).autoNumeric('get');


                system_category_id_and_taobao_category_id.index=i;
                system_category_id_and_taobao_category_id.taobao_category_id=taobao_category_id;
                system_category_id_and_taobao_category_id.taobao_link_category=taobao_link_category;
                system_category_id_and_taobao_category_id.filter_page_number=1;
                //category_id
                var category_id=$item_category.find('input[name="category_id[]"]').val();
                system_category_id_and_taobao_category_id.category_id=category_id;

                //page number
                var total_page=$item_category.find('select[name="total_page[]"]').val();
                system_category_id_and_taobao_category_id.total_page=parseInt(total_page);

                //filter by
                var filter_by=$item_category.find('select[name="filter_by[]"]').val();
                system_category_id_and_taobao_category_id.filter_by=filter_by;

                //filter by
                var $taobao_deal=$item_category.find('select[name="taobao_deal[]"]');
                if($taobao_deal.is(':checked')){
                    system_category_id_and_taobao_category_id.taobao_deal=$taobao_deal.val();
                }else{
                    system_category_id_and_taobao_category_id.taobao_deal=0;
                }







                list_system_category_id_and_taobao_category_id.push(system_category_id_and_taobao_category_id);
            }
            if(list_system_category_id_and_taobao_category_id.length==0){
                alert('there are no category selected or category tao bao link null or empty, please check agian');
            }else{
                plugin.settings.list_system_category_id_and_taobao_category_id=list_system_category_id_and_taobao_category_id;
                plugin.save_products_by_per_category();

            }

        }
        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_importproducttaobao_all = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_importproducttaobao_all')) {
                var plugin = new $.view_importproducttaobao_all(this, options);

                $(this).data('view_importproducttaobao_all', plugin);

            }

        });

    }

})(jQuery);
