(function ($) {    // here we go!    $.mod_search = function (element, options) {        // plugin's default options        var defaults = {            //main color scheme for mod_search            //be sure to be same as colors on main.css or custom-variables.less            module_id: 0,            style: "table",            key: "product",            params: {}        }        // current instance of the object        var plugin = this;        // this will hold the merged default, and user-provided options        plugin.settings = {}        var $element = $(element), // reference to the jQuery version of DOM element            element = element;    // reference to the actual DOM element        // the "constructor" method that gets called when the object is created        plugin.setup_template = function () {            var $item_product_template=$element.find('.search-result .product .body .item');            plugin.settings.item_product_template=$item_product_template.getOuterHTML();            $item_product_template.remove();            var $item_category_template=$element.find('.search-result .category .body .item');            plugin.settings.item_category_template=$item_category_template.getOuterHTML();            $item_category_template.remove();            var $item_keyword=$element.find('.area-suggestion .list-suggestion .item');            plugin.settings.item_keyword=$item_keyword.getOuterHTML();            $item_keyword.remove();            var $no_result=$element.find('.search-result .product .body .no-result');            plugin.settings.no_result=$no_result.getOuterHTML();            $no_result.remove();        };        plugin.fill_product_result = function (response) {            var $body=$element.find('.search-result .product .body');            $body.empty();            var products=response.products;            for(var i=0;i<products.length;i++){                var product=products[i];                var $item_product_template=$(plugin.settings.item_product_template);                $item_product_template.find('.product_code').html(product.product_code);                $item_product_template.find('.product_image').html(product.product_image);                $item_product_template.find('.product_name').html(product.product_name);                $item_product_template.find('.product_price').html(product.product_price);                $item_product_template.find('.product_link').attr('href',product.link);                $item_product_template.appendTo($body);            }            if(products.length==0){                $(plugin.settings.no_result).appendTo($body);            }            $body.find('div.product_name').dotdotdot();        };        plugin.fill_category_result = function (response) {            var $body=$element.find('.search-result .category .body');            $body.empty();            var categories=response.categories;            for(var i=0;i<categories.length;i++){                var category=categories[i];                var $item_category_template=$(plugin.settings.item_category_template);                $item_category_template.find('.category_image').html(category.category_image);                $item_category_template.find('.category_name').html(category.category_name);                $item_category_template.find('.category_link').attr('href',category.link);                $item_category_template.appendTo($body);            }            console.log($body);            if(categories.length==0){                $(plugin.settings.no_result).appendTo($body);            }            $body.find('div.category_name').dotdotdot();        };        plugin.fill_suggestion_result = function (list_suggestion) {            var $list_suggestion=$element.find('.area-suggestion .list-suggestion');            $list_suggestion.empty();            for(var i=0;i<list_suggestion.length;i++){                var item=list_suggestion[i];                var $item_keyword=$(plugin.settings.item_keyword);                $item_keyword.find('.item_keyword').html(item.item_keyword);                $item_keyword.appendTo($list_suggestion);            }            $list_suggestion.find('.item').hover(function(e){                $list_suggestion.find('.item').removeClass('active');                $(this).addClass('active');            });        };        plugin.fill_history_result = function (response) {            var $ul=$element.find('.search-result .history ul.list-history');            $ul.empty();            var list_history_keyword=response.list_history_keyword;            for(var i=0;i<list_history_keyword.length;i++){                var keyword=list_history_keyword[i];                var $keyword=$('<li  class="tags sub"><a href="javascript:void(0)">'+keyword+'</a></li>');                $keyword.appendTo($ul);            }            $ul.find('li.tags.sub a').click(function(){                var keyword=$(this).html();                $element.find('input[name="keyword"]').val(keyword);            });        };        plugin.ajax_search = function () {            var keyword=$element.find('input[name="keyword"]').val();            if(keyword.trim()==''){                return false;            }            var ajax_search=plugin.settings.ajax_search;            if(typeof ajax_search!='undefined'){                ajax_search.abort();            }            $element.find('.search-result .group-result').removeClass('show');            var key=$element.find('input[name="key"]').val();            var option_click = {                option: 'com_hikashop',                ctrl: 'product',                task: 'ajax_search_by_keyword',            };            option_click = $.param(option_click);            var data_submit = {                Itemid:$element.find('input[name="Itemid"]').val(),                key:$element.find('input[name="key"]').val(),                keyword:$element.find('input[name="keyword"]').val()            };            var $content_inner=$element.find('.search-result');            plugin.settings.ajax_search = $.ajax({                contentType: 'application/json',                type: "POST",                dataType: "json",                cache: true,                url: root_ulr + 'index.php?' + option_click,                data: JSON.stringify(data_submit),                beforeSend: function () {                    $content_inner.bho88loading();                },                success: function (response) {                    $content_inner.bho88loading(false);                    switch(key) {                        case 'product':                            $element.find('.search-result .product.group-result').addClass('show');                            plugin.fill_product_result(response);                            break;                        case 'category':                            $element.find('.search-result .category.group-result').addClass('show');                            plugin.fill_category_result(response);                            break;                        default:                            plugin.fill_all_result(response);                    }                    plugin.fill_history_result(response);                }            });        };        plugin.ajax_search_suggestion = function () {            var keyword=$element.find('input[name="keyword"]').val();            if(keyword.trim()==''){                return false;            }            var ajax_suggestion=plugin.settings.ajax_suggestion;            if(typeof ajax_suggestion!='undefined'){                ajax_suggestion.abort();            }            $element.find('.search-result .group-result').removeClass('show');            var key=$element.find('input[name="key"]').val();            var option_click = {                option: 'com_hikashop',                ctrl: 'product',                task: 'ajax_search_suggestion_by_keyword',            };            option_click = $.param(option_click);            var data_submit = {                Itemid:$element.find('input[name="Itemid"]').val(),                key:$element.find('input[name="key"]').val(),                keyword:$element.find('input[name="keyword"]').val()            };            plugin.settings.ajax_suggestion = $.ajax({                contentType: 'application/json',                type: "POST",                dataType: "json",                cache: true,                url: root_ulr + 'index.php?' + option_click,                data: JSON.stringify(data_submit),                beforeSend: function () {                },                success: function (response) {                    switch(key) {                        case 'product':                            plugin.fill_suggestion_result(response.products);                            break;                        case 'category':                            plugin.fill_suggestion_result(response.categories);                            break;                        default:                            plugin.fill_suggestion_result(response.products);                    }                }            });        };        plugin.init = function () {            plugin.settings = $.extend({}, defaults, options);            var params = plugin.settings.params;            var key = plugin.settings.key;            $element.find('a.key').click(function () {                var key = $(this).data('key');                var text = $(this).data('text');                var page_show_result = $(this).data('page_show_result');                var placeholder = $(this).data('placeholder');                $element.find('span.text').text(text);                $element.find('input[name="keyword"]').attr('placeholder', placeholder);                $element.find('input[name="Itemid"]').val(page_show_result);                $element.find('input[name="key"]').val(key);                var $input = $element.find('input[name="keyword"]');                $input.val("");            });            var $form = $element.find('form[name="search"]');            plugin.setup_template();            $form.submit(function () {                var list_language = plugin.settings.list_language;                var keyword = $form.find('input[name="keyword"]').val();                if (keyword.trim() == "") {                    $.alert_notify($.Jtext_('MOD_SEARCH_PLACE_INPUT_KEY_WORD', list_language),'error');                    $form.find('input[name="keyword"]').focus();                    return false;                }                plugin.ajax_search();                return false;            });            $element.find('.search-result a.close').click(function(e){                $element.find('.search-result').removeClass('show');            });            $element.find('.area-keyword .area-suggestion a.close').click(function(e){                $element.find('.area-keyword .area-suggestion').removeClass('show');            });            $element.find('a.delete').click(function(e){                var $content_inner=$element.find('.search-result');                $content_inner.bho88loading(false);                var ajax_search=plugin.settings.ajax_search;                if(typeof ajax_search!='undefined'){                    ajax_search.abort();                }                $element.find('input[name="keyword"]').val('');            });            $element.find('li.tags.sub a').click(function(){                var keyword=$(this).html();                $element.find('input[name="keyword"]').val(keyword);            });            $element.find('input[name="keyword"]').keydown(function(e){                var ajax_suggestion=plugin.settings.ajax_suggestion;                if(typeof ajax_suggestion!='undefined'){                    ajax_suggestion.abort();                }                switch(e.which) {                    case 37: // left                        $element.find('.area-keyword .area-suggestion').addClass('show');                        break;                    case 38: // up                        $element.find('.area-keyword .area-suggestion').addClass('show');                        $list_suggestion=$element.find('.area-suggestion .list-suggestion');                        var $active=$element.find('.area-suggestion .list-suggestion .item.active');                        var index=$active.index();                        $active.removeClass('active');                        if(index!=0)                        {                            $active=$($element.find('.area-suggestion .list-suggestion .item').get(index-1));                            $active.addClass('active');                        }                        $list_suggestion.scrollTo($active,20, {                            }                        );                        var text=$active.text().trim();                        if(text!="")                        {                            $element.find('input[name="keyword"]').val(text);                        }                        break;                    case 39: // right                        $element.find('.area-keyword .area-suggestion').addClass('show');                        break;                    case 13: // enter                        break;                    case 27: // enter                        $element.find('.area-keyword .area-suggestion').removeClass('show');                        break;                    case 40: // down                        $element.find('.area-keyword .area-suggestion').addClass('show');                        $list_suggestion=$element.find('.area-suggestion .list-suggestion');                        var $active=$element.find('.area-suggestion .list-suggestion .item.active');                        var total_item=$element.find('.area-suggestion .list-suggestion .item').length;                        var index=$active.index();                        $active.removeClass('active');                        if(index<total_item)                        {                            $active=$($element.find('.area-suggestion .list-suggestion .item').get(index+1));                            $active.addClass('active');                        }                        $list_suggestion.scrollTo($active,20,{                        });                        var text=$active.text().trim();                        if(text!="")                        {                            $element.find('input[name="keyword"]').val(text);                        }                        break;                    default:                        $element.find('.area-keyword .area-suggestion').addClass('show');                        plugin.ajax_search_suggestion();                        return; // exit this handler for other keys                }            });            $element.find('input[name="keyword"]').change(function(){                plugin.ajax_search();            });            $('body').click(function(e){                $target=$(e.target);                if ($target.closest('a.close').length) {                    return;                }                if ($target.closest('.search-form').length) {                    $element.addClass('active');                    $element.find('.search-result').addClass('show');                }else{                    $element.find('.search-result').removeClass('show');                    $element.removeClass('active');                }                if ($target.closest('.area-keyword').length) {                }else{                    $element.find('.area-keyword .area-suggestion').removeClass('show');                }            });            //$element.find('.search-result .product .body,.search-result .category .body').mCustomScrollbar();        }        plugin.example_function = function () {        }        plugin.init();    }    // add the plugin to the jQuery.fn object    $.fn.mod_search = function (options) {        // iterate through the DOM elements we are attaching the plugin to        return this.each(function () {            // if plugin has not already been attached to the element            if (undefined == $(this).data('mod_search')) {                var plugin = new $.mod_search(this, options);                $(this).data('mod_search', plugin);            }        });    }})(jQuery);