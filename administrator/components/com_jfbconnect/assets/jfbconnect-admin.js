/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */
if (typeof jfbcJQuery == "undefined")
    jfbcJQuery = jQuery;

var jfbcAdmin = {
    request: {
        send: function (start)
        {
            var statusDiv = jfbcJQuery('#sendStatus');
            if (start)
            {
                statusDiv.html("<h2>Sending in progress...</h2>This may take a few minutes. Please do not navigate away from this page until complete!");
            }
            jfbcAdmin.ajax('option=com_jfbconnect&controller=request&task=send', jfbcAdmin.request.updateStatus);
        },

        updateStatus: function (req)
        {
            var status = jfbcJQuery.parseJSON(req);
            var statusDiv = jfbcJQuery('#sendStatus');
            statusDiv.html("<h2>Sending in progress...</h2>" +
                "<p>This may take a few minutes. Please do not navigate away from this page until complete!<br/><br/>" +
                "<strong>Total Sent</strong>: " + status.sendCount + "<br/><i>(" + status.sendSuccess + " Successful / " +
                status.sendFail + " Fail)</i></p>");

            if (status.inProgress)
                jfbcAdmin.request.send(false);
            else
                statusDiv.html("<h2>Send Complete!</h2>" +
                    "<p><strong>Total Sent</strong>: " + status.sendCount + "<br/><i>(" + status.sendSuccess + " Successful / " +
                    status.sendFail + " Fail)</i></p>" +
                    "<h4><a href=\"index.php?option=com_jfbconnect&controller=notification&task=display&requestid=" + status.requestId + "\">Go to the Notifications Area for this Request</a></h4>");
        }
    },

    ajax: function (url, callback)
    {
        jfbcJQuery.ajax({url: 'index.php',
                data: url}
        ).done(callback);
    },

    autotune: {
        enablePlugin: function (name, status)
        {
            form = document.getElementById('adminForm');
            form.pluginName.value = name;
            form.pluginStatus.value = status;
            form.task.value = 'publishPlugin';
            form.submit();
        }
    },

    scsocialwidget: {
        fetchWidgets: function (name)
        {
            jfbcAdmin.ajax('option=com_jfbconnect&controller=ajax&task=scsocialprovider&provider=' + name + '&id=' + sc_modid, jfbcAdmin.scsocialwidget.showWidgets);
        },

        showWidgets: function (ret)
        {
            jfbcJQuery('#widget_settings').html("");
            jfbcJQuery('#widget_list').html(ret);
            jfbcJQuery("jform[params][widget_type]").val("widget");
        },

        fetchSettings: function (name)
        {
            if (name == 'widget')
                jfbcJQuery('#widget_settings').html("");
            else
            {
                var provider = jfbcJQuery("#jform_params_provider_type").val();
                jfbcAdmin.ajax('option=com_jfbconnect&controller=ajax&task=scsocialwidget&provider=' + provider + '&name=' + name + '&id=' + sc_modid, jfbcAdmin.scsocialwidget.showSettings);
            }
        },

        showSettings: function (ret)
        {
            jfbcJQuery('#widget_settings').html(ret);

            $$('.hasTip').each(function(el) {
                var title = el.get('title');
                if (title) {
                    var parts = title.split('::', 2);
                    el.store('tip:title', parts[0]);
                    el.store('tip:text', parts[1]);
                }
            });
            var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false});
        }
    },

    channels: {
        outbound: {
            fetchChannels: function (name)
            {
                jfbcJQuery('#channel-attribs').html('<div class="jfbc-error">' + jfbc_language_select_provider + '</div>');
                if (name != '--')
                    jfbcAdmin.ajax('option=com_jfbconnect&controller=ajax&task=channelGetOutboundChannels&provider=' + name, jfbcAdmin.channels.outbound.showChannels);
            },
            showChannels: function (ret)
            {
                jfbcJQuery('#channel-attribs').html('<div class="jfbc-error">' + jfbc_language_select_provider + '</div>');
                jfbcJQuery('#jform_type').parent().html(ret);
            },
            fetchChannelSettings: function (name)
            {
                var provider = jfbcJQuery("#jform_provider").val();
                jfbcAdmin.ajax('option=com_jfbconnect&controller=ajax&task=channelGetOutboundChannelSettings&provider=' + provider + '&channel=' + name, jfbcAdmin.channels.outbound.showSettings);
            },
            showSettings: function (ret)
            {
                jfbcJQuery('#channel-attribs').html('<div class="jfbc-error">' + jfbc_language_click_save + '</div>');
//                jfbcJQuery('#channel-attribs').html(ret);
            }
        }
    },
    checkForUpdate: function ()
    {
        /**
         * @copyright    Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
         * @license        GNU General Public License version 2 or later; see LICENSE.txt
         */
        jfbcJQuery(document).ready(function ()
        {
            var ajax_structure = {
                success: function (data, textStatus, jqXHR)
                {
                    try
                    {
                        var updateInfoList = jfbcJQuery.parseJSON(data);
                    } catch (e)
                    {
                        // An error occured
                        jfbcJQuery('#jfbconnect_update_icon').find('span').html(jfbconnect_update_text.ERROR);
                    }
                    if (updateInfoList instanceof Array)
                    {
                        if (updateInfoList.length < 1)
                        {
                            // No updates
                            jfbcJQuery('#jfbconnect_update_icon').find('span').html(jfbconnect_update_text.UPTODATE);
                        } else
                        {
                            jfbcJQuery('#jfbconnect_update_icon').find('span').html(jfbconnect_update_text.UPTODATE);
                            jfbcJQuery.each(updateInfoList, function (k, v)
                            {
                                if (v.extension_id == jfbconnect_extension_id)
                                    jfbcJQuery('#jfbconnect_update_icon').find('span').html(jfbconnect_update_text.UPDATEFOUND);
                            });
                        }
                    } else
                    {
                        // An error occured
                        jfbcJQuery('#jfbconnect_update_icon').find('span').html(jfbconnect_update_text.ERROR);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown)
                {
                    // An error occured
                    jfbcJQuery('#jfbconnect_update_icon').find('span').html(jfbconnect_update_text.ERROR);
                },
                url: jfbconnect_update_ajax_url + '&eid=0&skip=700'
            };
            ajax_object = new jfbcJQuery.ajax(ajax_structure);
        });
    }
}