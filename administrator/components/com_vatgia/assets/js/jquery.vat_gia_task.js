//huong dan su dung
/*
 $('.vat_gia_task').vat_gia_task();

 vat_gia_task=$('.vat_gia_task').data('vat_gia_task');
 console.log(vat_gia_task);
 */

// jQuery Plugin for SprFlat admin vat_gia_task
// Control options and basic function of vat_gia_task
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function($) {

    // here we go!
    $.vat_gia_task = function(element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for vat_gia_task
            //be sure to be same as colors on main.css or custom-variables.less

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
            var list_link=[];
            $element.find('#container_content .shop_list li a').each(function(){
                list_link.push($(this).attr('href'));
            });
            var option_click= {

                option: 'com_vatgia',


                task: 'vatgia.ajax_save_list_link_category'



            };
            option_click= $.param(option_click);
            var data_submit={};
            data_submit.list_link=list_link;
            var ajax_web_design=$.ajax({

                contentType: 'application/json',

                type: "POST",

                dataType: "json",

                url: root_ulr+'index.php?'+option_click,

                data: JSON.stringify(data_submit),

                beforeSend: function () {

                    $element.bho88loading();

                },

                success: function (response) {

                    $element.bho88loading(true);

                    if(response.e==0)

                    {

                    }else if(response.e==1){

                        alert(response.m);

                    }







                }

            });

        };



        plugin.example_function = function() {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.vat_gia_task = function(options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function() {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('vat_gia_task')) {
                var plugin = new $.vat_gia_task(this, options);

                 $(this).data('vat_gia_task', plugin);

            }

        });

    }

})(jQuery);
