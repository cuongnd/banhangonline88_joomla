//huong dan su dung/* $('.view_product_show').view_product_show(); view_product_show=$('.view_product_show').data('view_product_show'); console.log(view_product_show); */// jQuery Plugin for SprFlat admin view_product_show// Control options and basic function of view_product_show// version 1.0, 28.02.2013// by SuggeElson www.suggeelson.com(function ($) {    // here we go!    $.view_product_show = function (element, options) {        // plugin's default options        var defaults = {            product:{},            list_language:{}            //main color scheme for view_product_show            //be sure to be same as colors on main.css or custom-variables.less        }        // current instance of the object        var plugin = this;        // this will hold the merged default, and user-provided options        plugin.settings = {}        var $element = $(element), // reference to the jQuery version of DOM element            element = element;    // reference to the actual DOM element        // the "constructor" method that gets called when the object is created        plugin.init = function () {            plugin.settings = $.extend({}, defaults, options);            var product=plugin.settings.product;            var list_language=plugin.settings.list_language;            var product_sale_end=product.product_sale_end;            console.log(product_sale_end);            console.log(list_language);/*            $element.find(".since-start").countdown({                render: function (date) {                    var years=date.years;                    var days=date.days;                    var hours=date.hours;                    var min=date.min;                    var sec=date.sec;                    var html='';                    if(years>0)                    {                        html=$.vsprintf( list_language.HIKA_COUNT_DOWN_YEAR,[ years, days,hours,min,sec] );                    }else if(days>0){                        html=$.vsprintf( list_language.HIKA_COUNT_DOWN_DAY, [days, hours,min,sec]);                    }else if(hours>0){                        html=$.vsprintf( list_language.HIKA_COUNT_DOWN_HOUR, [ hours,min,sec ] );                    }else if(min>0){                        html=$.vsprintf( list_language.HIKA_COUNT_DOWN_MINITER, [ min,sec]  );                    }else if(sec>0){                        html=$.vsprintf( list_language.HIKA_COUNT_DOWN_SECOUND,[ sec]);                    }                    this.el.innerHTML = html;                }            });*/        }        plugin.example_function = function () {        }        plugin.init();    }    // add the plugin to the jQuery.fn object    $.fn.view_product_show = function (options) {        // iterate through the DOM elements we are attaching the plugin to        return this.each(function () {            // if plugin has not already been attached to the element            if (undefined == $(this).data('view_product_show')) {                var plugin = new $.view_product_show(this, options);                $(this).data('view_product_show', plugin);            }        });    }})(jQuery);