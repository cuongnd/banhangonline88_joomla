
//huong dan su dung
/*
 $('.core_website').core_website();

 core_website=$('.core_website').data('core_website');
 console.log(core_website);
 */

// jQuery Plugin for SprFlat admin core_website
// Control options and basic function of core_website
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {

    // here we go!
    $.core_website = function (element, options) {

        // plugin's default options
        var defaults = {
            //main color scheme for core_website
            //be sure to be same as colors on main.css or custom-variables.less
            enqueueMessage:""
        }
        // current instance of the object
        var plugin = this;
        // this will hold the merged default, and user-provided options
        plugin.settings = {}
        var $element = $(element), // reference to the jQuery version of DOM element
            element = element;    // reference to the actual DOM element
        // the "constructor" method that gets called when the object is created
        plugin.init = function () {
            plugin.settings = $.extend({}, defaults, options);
            var enqueueMessage=plugin.settings.enqueueMessage;
            var message=enqueueMessage.message;
            var type=enqueueMessage.type;
            if(typeof message!="undefined" && message!=''){
                $.alert_notify(message,type,{
                    allow_dismiss:true,
                    timer:300000,
                    z_index:99999
                });
            }

            $(window).on('ajaxComplete', function() {
                setTimeout(function() {
                    $(window).lazyLoadXT();
                }, 50);
            });
            var y = $(window).scrollTop();  //your current y position on the page
            $(window).scrollTop(y+1);
            $('.google-plus-login').click(function(){
                var current_return=$(this).data('return');
                var href=$(this).data('href');
                var option_click = {
                    option: 'com_users',
                    task: 'user.save_session_current_return',
                };
                option_click = $.param(option_click);
                var data_submit = {};
                data_submit.current_return =current_return;
                var ajax_web_design = $.ajax({
                    contentType: 'application/json',
                    type: "POST",
                    dataType: "json",
                    url: root_ulr + 'index.php?' + option_click,
                    data: JSON.stringify(data_submit),
                    beforeSend: function () {
                        $('body').bho88loading();
                    },
                    success: function (response) {
                        window.location.href = href;
                    }
                });
            });

            /*
             ///Socket.io
             var socket = io.connect();
             socket.on('connect', function() {
             console.log('connected');
             });
            socket.on('nbUsers', function(msg) {
                $("#nbUsers").html(msg.nb);
            });
            socket.on('message', function(data) {
                addMessage(data['message'], data['pseudo'], new Date().toISOString(), false);
                console.log(data);
            });

            //Help functions
            function sentMessage() {
                if (messageContainer.val() != "")
                {
                    if (pseudo == "")
                    {
                        $('#modalPseudo').modal('show');
                    }
                    else
                    {
                        socket.emit('message', messageContainer.val());
                        addMessage(messageContainer.val(), "Me", new Date().toISOString(), true);
                        messageContainer.val('');
                        submitButton.button('loading');
                    }
                }
            }
            function addMessage(msg, pseudo, date, self) {
                if(self) var classDiv = "row message self";
                else var classDiv = "row message";
                $("#chatEntries").append('<div class="'+classDiv+'"><p class="infos"><span class="pseudo">'+pseudo+'</span>, <time class="date" title="'+date+'">'+date+'</time></p><p>' + msg + '</p></div>');
                time();
            }

            function bindButton() {
                submitButton.button('loading');
                messageContainer.on('input', function() {
                    if (messageContainer.val() == "") submitButton.button('loading');
                    else submitButton.button('reset');
                });
            }
            function setPseudo() {
                if ($("#pseudoInput").val() != "")
                {
                    socket.emit('setPseudo', $("#pseudoInput").val());
                    socket.on('pseudoStatus', function(data){
                        if(data == "ok")
                        {
                            $('#modalPseudo').modal('hide');
                            $("#alertPseudo").hide();
                            pseudo = $("#pseudoInput").val();
                        }
                        else
                        {
                            $("#alertPseudo").slideDown();
                        }
                    })
                }
            }
            function time() {
                $("time").each(function(){
                    $(this).text($.timeago($(this).attr('title')));
                });
            }
            function setHeight() {
                $(".slimScrollDiv").height('603');
                $(".slimScrollDiv").css('overflow', 'visible')
            }
*/

        }
        plugin.example_function = function () {
        }
        plugin.init();
    }
    // add the plugin to the jQuery.fn object
    $.fn.core_website = function (options) {

        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {

            // if plugin has not already been attached to the element
            if (undefined == $(this).data('core_website')) {
                var plugin = new $.core_website(this, options);
                $(this).data('core_website', plugin);
            }
        });
    }
})(jQuery);
