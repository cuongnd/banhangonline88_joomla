//huong dan su dung
/*
 $('.view_productmarket_form').view_productmarket_form();

 view_productmarket_form=$('.view_productmarket_form').data('view_productmarket_form');
 console.log(view_productmarket_form);
 */

// jQuery Plugin for SprFlat admin view_productmarket_form
// Control options and basic function of view_productmarket_form
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function ($) {

    // here we go!
    $.view_productmarket_form = function (element, options) {

        // plugin's default options
        var defaults = {
            show_help:true,
            enable_audio:true
            //main color scheme for view_productmarket_form
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
            var $item_element = $element.find('.btn.cartlink');

            $item_element.attr('data-intro', "Giỏ hàng liên kết");

            var $item_element = $element.find('.btn.apply');

            $item_element.attr('data-intro', "Trong quá trình cập nhật dữ liệu hãy lưu lại thường xuyên mà không đóng của cửa sổ để tránh mất dữ liệu");

            var $item_element = $element.find('.btn.save');

            $item_element.attr('data-intro', "Khi cập nhật dữ liệu hoàn tất vui lòng nhấn vào đây để hoàn thành, đồng thời đóng của sổ");

            var $item_element = $element.find('.images-library');

            $item_element.attr('data-intro', "Vui lòng nhập thư viện ảnh sản phẩm vào khu vực này");

            var $item_element = $element.find('#hikamarket_product_image_uploadpopup');

            $item_element.attr('data-intro', "Nhấn vào đây để nhập ảnh từ máy tính của bạn");

            var $item_element = $element.find('#hikamarket_product_image_addpopup');

            $item_element.attr('data-intro', "Nhấn vào đây để nhập ảnh từ dữ liệu các ảnh mà trước đây bạn upload");

            var $item_element = $element.find('.hikamarket_product_main_image_thumb');

            $item_element.attr('data-intro', "Nhấn và giữ, đồng thời di chuyển để sắp sếp vị trí các ảnh theo ý muốn,Muốn xem ảnh, và đồng thời chỉnh sửa mô tả cho ảnh vui lòng nhấn vào đây");

            var $item_element = $element.find('input[name="data[product][product_code]"]');

            $item_element.attr('data-intro', "Nhập tên sản phẩm ở đây");
        };
        plugin.init = function () {
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

        plugin.example_function = function () {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_productmarket_form = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_productmarket_form')) {
                var plugin = new $.view_productmarket_form(this, options);

                $(this).data('view_productmarket_form', plugin);

            }

        });

    }

})(jQuery);
