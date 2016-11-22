//huong dan su dung
/*
 $('.load_page').load_page();

 load_page=$('.load_page').data('load_page');
 console.log(load_page);
 */

// jQuery Plugin for SprFlat admin load_page
// Control options and basic function of load_page
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.load_page = function(element, options) {

        // plugin's default options
        var defaults = {
            show_help:true,
            enable_audio:true
            //main color scheme for load_page
            //be sure to be same as colors on main.css or custom-variables.less

        }

        // current instance of the object
        var plugin = this;

        // this will hold the merged default, and user-provided options
        plugin.settings = {}

        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element

        // the "constructor" method that gets called when the object is created
        plugin.set_help = function () {
            var i = 1;
            var $item_element = $element.find('.menu-iem.menu-iem-716.level-1');

            $item_element.attr('data-intro', "Đăng ký trở thành nhà bán hàng");

            var $item_element = $element.find('.menu-iem.menu-iem-1111.level-1');

            $item_element.attr('data-intro', "Khu vực quản lý sản phẩm");


            var $item_element = $element.find('.menu-iem.menu-iem-3023.level-1');

            $item_element.attr('data-intro', "Cung cấp cho bạn danh sách các nhà cung cấp");

            var $item_element = $element.find('#mod_wishlist_337');

            $item_element.attr('data-intro', "Khu vực này lưu các sản phẩm yêu thích của bạn");


            var $item_element = $element.find('#mod_cart_336');

            $item_element.attr('data-intro', "giỏ hàng của bạn");


            var $item_element = $element.find('#acymailing_module_formAcymailing40611');

            $item_element.attr('data-intro', "Nếu bạn muốn nhận các bản tin khuyến mãi, các sản phẩm giảm giá xin vui lòng điền thông tin vào đây");


            var $item_element = $element.find('#mod_search_335');

            $item_element.attr('data-intro', "Khu vực tìm kiếm sản phẩm, Ở đây bạn có thể tìm kiếm, các sản phẩm, như sản phẩm giảm giá,tìm kiếm rao vặt, tìm kiếm các nhà cung cấp");


            var $item_element = $element.find('#mod_menu_114');

            $item_element.attr('data-intro', "Bạn có thể tìm kiếm dễ dàng các sản phẩm theo các danh mục sản phẩm dưới đây");

            var $item_element = $element.find('#mod_tab_products_340');

            $item_element.attr('data-intro', "Danh sách các nhà cung cấp uy tín");

            var $item_element = $element.find('#mod_tab_products_334');

            $item_element.attr('data-intro', "Các sản phẩm bán chạy nhất tuần");


            var $item_element = $element.find('#mod_custom_115');

            $item_element.attr('data-intro', "Để có thể mua hàng trên điện thoại di động vui lòng tải ứng dụng ở đây");


            var $item_element = $element.find('#mod_tab_products_338');

            $item_element.attr('data-intro', "Ở đây bạn có thể dễ dàng tìm kiếm được các sản phẩm hót nhất");

            var $item_element = $element.find('#mod_tab_products_339');

            $item_element.attr('data-intro', "Ở đây bạn có thể dễ dàng tìm kiếm được các sản phẩm mới nhất");


            var $item_element = $element.find('#mod_menu_157');

            $item_element.attr('data-intro', " Nếu bạn cần hỗ trợ xin vui lòng tìm kiếm các hỗ trợ ở đây");


            var $item_element = $element.find('#mod_menu_159');

            //$item_element.attr('data-intro', "Nếu bạn là nhà cung cấp bạn có thể quản lý sản phẩm, quản lý các đơn hàng,sửa thông tin nhà cung cấp");
            $item_element.attr('data-intro', "bạn bơ và bạn hiệp chuẩn bị đi đánh răng và rửa mặt để đi ngủ");


        };

        plugin.init = function() {
            plugin.settings = $.extend({}, defaults, options);
            var show_help=plugin.settings.show_help;
            var enable_audio=plugin.settings.enable_audio;
            if(show_help) {
                plugin.set_help();
                var help_tour = introJs();
                help_tour.setOption('tooltipPosition', 'auto');
                help_tour.setOption('teletype', true);
                help_tour.setOption('enable_audio', enable_audio);
                help_tour.setOption('positionPrecedence', ['left', 'right', 'bottom', 'top']);
                help_tour.start();
            }

        }

        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.load_page = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('load_page')) {
                var plugin = new $.load_page(this, options);

                $(this).data('load_page', plugin);

            }

        });

    }

})(jQuery);
