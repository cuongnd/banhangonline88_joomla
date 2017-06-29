//huong dan su dung
/*
 $('.view_vendormarket_import_vendor_from_vatgia').view_vendormarket_import_vendor_from_vatgia();

 view_vendormarket_import_vendor_from_vatgia=$('.view_vendormarket_import_vendor_from_vatgia').data('view_vendormarket_import_vendor_from_vatgia');
 console.log(view_vendormarket_import_vendor_from_vatgia);
 */

// jQuery Plugin for SprFlat admin view_vendormarket_import_vendor_from_vatgia
// Control options and basic function of view_vendormarket_import_vendor_from_vatgia
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.view_vendormarket_import_vendor_from_vatgia = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for view_vendormarket_import_vendor_from_vatgia
            //be sure to be same as colors on main.css or custom-variables.less

        }

        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        plugin.settings = {}

        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element

        plugin.fill_vendor = function(response) {
            var html=response.html;
            html=$.base64Decode(html);
            $element.find('.list-vendor').empty();
            $element.find('.list-vendor').html(html);
            $element.find('.link').html(response.link);
            $element.find('.total_page').html(response.total_page);
            plugin.save_list_vendor();
        }
        plugin.save_list_vendor = function() {
            var $list_tr=$element.find('.list-vendor').find('tr:not(.tr.text_title)');
            var list_vendor=[];
            for(var i=0;i<$list_tr.length;i++){
                var $tr=$($list_tr.get(i));
                vendor_item={};
                vendor_item.vendor_name=$tr.find('.company_name a').html();
                vendor_item.vatgia_link=$tr.find('.company_name a').attr('href');
                vendor_item.vendor_address_company =$tr.find('.address').html();
                var vendor_address_telephone =$tr.find('.phone').html();
                if(typeof vendor_address_telephone!="undefined")
                {
                    vendor_address_telephone=vendor_address_telephone.replace('Điện thoại : ','');
                }
                vendor_item.vendor_address_telephone =vendor_address_telephone;
                vendor_item.website  =$tr.find('.website a').attr('href');
                vendor_item.yahoo  =$tr.find('.yahoo').html();
                vendor_item.image  =$tr.find('.picture_x_small img').attr('src');
                list_vendor.push(vendor_item);
            }
            current_index=plugin.settings.current_index;
            $.ajax({
                type: "POST",
                url: 'index.php?save_vendor',
                dataType: "json",
                data: (function () {

                    dataPost = {
                        option: 'com_hikamarket',
                        ctrl:"vendor",
                        task: 'save_list_vendor',
                        current_index: current_index,
                        list_vendor: list_vendor

                    };
                    return dataPost;
                })(),
                beforeSend: function () {
                },
                error: function(){
                },
                success: function (response) {
                    plugin.get_list_vendor();

                }
            });

        }
        plugin.get_list_vendor = function() {
            plugin.settings.ajax_get_list_vendor= $.ajax({
                type: "POST",
                url: 'index.php',
                dataType: "json",
                data: (function () {

                    dataPost = {
                        option: 'com_hikamarket',
                        ctrl:"vendor",
                        task: 'get_list_vendor'

                    };
                    return dataPost;
                })(),
                beforeSend: function () {
                },
                error: function(){
                },
                success: function (response) {
                    plugin.settings.current_index=response.current_index;
                    plugin.fill_vendor(response);

                }
            });

        }
        plugin.init = function() {
            $element.find('.btn-import-vendor').prop('disabled',false);
            $element.find('.btn-import-vendor').click(function(){
                $element.find('.btn-import-vendor').prop('disabled',true);
                plugin.get_list_vendor();
            });
            $element.find('.btn-cancel-import-vendor').click(function(){
                plugin.settings.ajax_get_list_vendor.abort();
                $element.find('.btn-import-vendor').prop('disabled',false);
            });

        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_vendormarket_import_vendor_from_vatgia = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_vendormarket_import_vendor_from_vatgia')) {
                var plugin = new $.view_vendormarket_import_vendor_from_vatgia(this, options);

                $(this).data('view_vendormarket_import_vendor_from_vatgia', plugin);

            }

        });

    }

})(jQuery);
