//huong dan su dung
/*
 $('.view_login_default').view_login_default();

 view_login_default=$('.view_login_default').data('view_login_default');
 console.log(view_login_default);
 */

// jQuery Plugin for SprFlat admin view_login_default
// Control options and basic function of view_login_default
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com

(function ($) {

    // here we go!
    $.view_login_default = function (element, options) {

        // plugin's default options
        var defaults = {
            show_help: true,
            enable_audio: true,
            key_dont_show_agian: "",
            user_dont_show_help: true,
            list_messenger: [],
            option_alert:{
                allow_dismiss:true,
                timer:30000,
                z_index:99999
            }
            //main color scheme for view_login_default
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
            var list_messenger = plugin.settings.list_messenger;

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
            var option_alert = plugin.settings.option_alert;
            var list_messenger=plugin.settings.list_messenger;
            var $form = $element.find('#hikamarket_registration_form');
            var $name = $form.find('input[name="data[register][name]"]');
            var $email = $form.find('input[name="data[register][email]"]');
            var $password = $form.find('input[name="data[register][password]"]');
            var $password2 = $form.find('input[name="data[register][password2]"]');
            var $vendor_name = $form.find('input[name="data[vendorregister][vendor_name]"]');
            var vendor_terms=tinymce.get("vendor_terms").getContent();
            if ($name.val().trim() == '') {
                $.alert_notify(list_messenger['HIKA_NAME_REQUIRED'],'error',option_alert);
                $name.focus();
                return false;
            }else if($email.val().trim()==''){
                $.alert_notify(list_messenger['HIKA_EMAIL_REQUIRED'],'error',option_alert);
                $email.focus();
                return false;
            }else if($password.val().trim()==''){
                $.alert_notify(list_messenger['HIKA_PASSWORD_REQUIRED'],'error',option_alert);
                $password.focus();
                return false;
            }else if($password2.val().trim()==''){
                $.alert_notify(list_messenger['HIKA_PASSWORD_RETYPE_REQUIRED'],'error',option_alert);
                $password2.focus();
                return false;
            }else if(!$.is_email($email.val().trim())){
                $.alert_notify(list_messenger['HIKA_EMAIL_INCORRECT'],'error',option_alert);
                $email.focus();
                return false;
            }else if($password.val().trim()!=$password2.val().trim()){
                $.alert_notify(list_messenger['HIKA_PASSWORD_RETYPE_INCORRECT'],'error',option_alert);
                $password2.focus();
                return false;
            }else if($vendor_name.val().trim()==''){
                $.alert_notify(list_messenger['HIKA_VENDOR_NAME_REQUIRED'],'error',option_alert);
                $vendor_name.focus();
                return false;
            }else if(vendor_terms.trim()==''){
                $.alert_notify(list_messenger['HIKAM_ERR_TERMS_EMPTY'],'error',option_alert);
                $vendor_terms.focus();
                return false;
            }

            return true;
        }
        plugin.ajax_registration= function () {
            var $form = $element.find('#hikamarket_registration_form');
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
            var vendor_terms=tinymce.get("vendor_terms").getContent();
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
                            timer:30000,
                            z_index:99999
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
        plugin.get_google_plus_login = function () {
            var option_click = {
                option: "com_easysocial",
                ctrl: "users",
                task: "get_google_plus_login",
                format: 'json',
                tmpl: 'json',
                ignoreMessages: true
            };
            var data_submit={};
            option_click = $.param(option_click);

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
                    var auth_url=response.auth_url;
                    window.location.href = auth_url;

                }
            });
        };
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
            $element.find('a.get-google-plus-login').click(function (e) {
                plugin.get_google_plus_login();
            });

        }

        plugin.example_function = function () {

        }
        plugin.init();

    }

    // add the plugin to the jQuery.fn object
    $.fn.view_login_default = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('view_login_default')) {
                var plugin = new $.view_login_default(this, options);

                $(this).data('view_login_default', plugin);

            }

        });

    }

})(jQuery);
