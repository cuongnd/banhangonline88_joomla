//huong dan su dung
/*
 $('.view_vendormarket_form').view_vendormarket_form();

 view_vendormarket_form=$('.view_vendormarket_form').data('view_vendormarket_form');
 console.log(view_vendormarket_form);
 */

// jQuery Plugin for SprFlat admin view_vendormarket_form
// Control options and basic function of view_vendormarket_form
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function ($) {

    // here we go!
    $.view_vendormarket_form = function (element, options) {

        // plugin's default options
        var defaults = {
            show_help: true,
            enable_audio: true,
            key_dont_show_agian: "",
            user_dont_show_help: true,
            list_messenger: []
            //main color scheme for view_vendormarket_form
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
            list_messenger = plugin.settings.list_messenger;
            var i = 1;
            var $item_element = $element.find('#hikamarket_registration_form .es-signin-social');
            $item_element.attr('data-intro', "fdgfdgf");
        };
        plugin.close_help_tour = function () {
            var help_tour = plugin.settings.help_tour;
            var key_dont_show_agian = plugin.settings.key_dont_show_agian;
            var help_dont_show_again = help_tour._options.help_dont_show_again;
            var option_click = {
                option: "com_hikamarket",
                ctrl: "vendor",
                task: "ajax_change_status_help_dont_show_again",
                format: 'json',
                help_dont_show_again: help_dont_show_again,
                key_dont_show_agian: key_dont_show_agian,
                tmpl: 'json',
                ignoreMessages: true
            };
            option_click = $.param(option_click);
            var data_submit = {};
            var ajax_web_design = $.ajax({
                contentType: 'application/json',
                type: "POST",
                dataType: "json",
                url: root_ulr + '/index.php?' + option_click,
                data: JSON.stringify(data_submit),
                beforeSend: function () {
                    $('.div-loading').show();
                },
                success: function (response) {
                    $('.div-loading').hide();


                }
            });
        }
        plugin.registion_form_validate = function () {
            var list_messenger=plugin.settings.list_messenger;
            var $form = $element.find('#hikamarket_registration_form');
            var $name = $form.find('input[name="data[register][name]"]');
            var $email = $form.find('input[name="data[register][email]"]');
            var $password = $form.find('input[name="data[register][password]"]');
            var $password2 = $form.find('input[name="data[register][password2]"]');
            var $vendor_name = $form.find('input[name="data[vendorregister][vendor_name]"]');
            var $vendor_terms=$element.find('#vendor_terms');
            if ($name.val().trim() == '') {
                $.alert_notify(list_messenger['HIKA_NAME_REQUIRED'],'error');
                $name.focus();
                return false;
            }else if($email.val().trim()==''){
                $.alert_notify(list_messenger['HIKA_EMAIL_REQUIRED'],'error');
                $email.focus();
                return false;
            }else if($password.val().trim()==''){
                $.alert_notify(list_messenger['HIKA_PASSWORD_REQUIRED'],'error');
                $password.focus();
                return false;
            }else if($password2.val().trim()==''){
                $.alert_notify(list_messenger['HIKA_PASSWORD_RETYPE_REQUIRED'],'error');
                $password2.focus();
                return false;
            }else if(!$.is_email($email.val().trim())){
                $.alert_notify(list_messenger['HIKA_EMAIL_INCORRECT'],'error');
                $email.focus();
                return false;
            }else if($password.val().trim()!=$password2.val().trim()){
                $.alert_notify(list_messenger['HIKA_PASSWORD_RETYPE_INCORRECT'],'error');
                $password2.focus();
                return false;
            }else if($vendor_name.val().trim()==''){
                $.alert_notify(list_messenger['HIKA_VENDOR_NAME_REQUIRED'],'error');
                $vendor_name.focus();
                return false;
            }else if($vendor_terms.val().trim()==''){
                $.alert_notify(list_messenger['HIKAM_ERR_TERMS_EMPTY'],'error');
                $vendor_terms.focus();
                return false;
            }

            return true;
        }
        plugin.ajax_registration= function () {
            $form = $element.find('#hikamarket_registration_form');
            var option_click = {
                option: "com_hikamarket",
                ctrl: "vendor",
                task: "ajax_vendor_registration",
                format: 'json',
                tmpl: 'json',
                ignoreMessages: true
            };
            option_click = $.param(option_click);
            var data_submit = $form.serializeObject();
            var vendor_description=$element.find('#vendor_description').val();
            data_submit.data.vendorregister.vendor_description=vendor_description;
            var vendor_terms=$element.find('#vendor_terms').val();
            data_submit.data.vendorregister.vendor_terms=vendor_terms;
            var ajax_web_design = $.ajax({
                contentType: 'application/json',
                type: "POST",
                dataType: "json",
                url: root_ulr + '/index.php?' + option_click,
                data: JSON.stringify(data_submit),
                beforeSend: function () {
                    $('.div-loading').show();
                },
                success: function (response) {
                    console.log(response);
                    $('.div-loading').hide();
                    if(response.e==1&&response.type=="email_exists"){
                        $.alert_notify(response.m,'error',{
                            allow_dismiss:true,
                            timer:30000
                        });
                        $email.focus();
                    }else if(response.e==1&&response.type=="terms_empty"){
                        $.alert_notify(response.m,'error');
                        $element.find('#vendor_terms').focus()
                    }
                    else {
                        $form.submit();
                    }

                }
            });
        }
        plugin.init = function () {
            plugin.settings = $.extend({}, defaults, options);
            var show_help = plugin.settings.show_help;
            var user_dont_show_help = plugin.settings.user_dont_show_help;
            var enable_audio = plugin.settings.enable_audio;
            plugin.set_help();
            var help_tour = introJs();
            help_tour.setOption('tooltipPosition', 'auto');
            help_tour.setOption('teletype', false);
            help_tour.setOption('help_dont_show_again', user_dont_show_help);
            help_tour.setOption('auto_play', true);
            help_tour.setOption('enable_audio', enable_audio);
            help_tour.setOption('positionPrecedence', ['left', 'right', 'bottom', 'top']);
            help_tour.onexit(function () {
                plugin.close_help_tour();
            });
            help_tour.oncomplete(function () {
                plugin.close_help_tour();
            });
            plugin.settings.help_tour = help_tour;
            if (user_dont_show_help == 0 && show_help) {
                help_tour.start();
            }
            $element.find('a.btn-link.help').click(function () {
                help_tour.start();

            });
            $element.find('button.btn-register').click(function (e) {
                if (plugin.registion_form_validate()) {
                    plugin.ajax_registration();
                }
            });

        }

        plugin.example_function = function () {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_vendormarket_form = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_vendormarket_form')) {
                var plugin = new $.view_vendormarket_form(this, options);

                $(this).data('view_vendormarket_form', plugin);

            }

        });

    }

})(jQuery);
