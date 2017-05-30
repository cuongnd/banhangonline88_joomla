function resetFilter(apply)
{
    if (document.adminForm.filter_assign) {
        document.adminForm.filter_assign.value = "M";
    }
    if (document.adminForm.id_client) {
        document.adminForm.id_client.value = "0";
    }
    if (document.adminForm.id_status) {
        document.adminForm.id_status.value = "WIP";
    }
    if (document.adminForm.filter_status) {
        document.adminForm.filter_status.value = "WIP";
    }
    if (document.adminForm.id_user) {
        document.adminForm.id_user.value = "0";
    }
    if (document.adminForm.filter_tickets) {
        document.adminForm.filter_tickets.value = "0";
    }
    if (document.adminForm.filter_category) {
        document.adminForm.filter_category.value = "";
    }
    if (document.adminForm.filter_search) {
        document.adminForm.filter_search.value = "";
    }
    if (document.adminForm.searchuser) {
        document.adminForm.searchuser.value = "";
    }
    if (document.adminForm.filter_workgroup) {
        document.adminForm.filter_workgroup.value = "0";
    }
    if (document.adminForm.filter_client) {
        document.adminForm.filter_client.value = "0";
    }
    if (document.adminForm.filter_user) {
        document.adminForm.filter_user.value = "0";
    }
    if (document.adminForm.ac_me) {
        document.adminForm.ac_me.value = "";
    }
}

function trimNumber(s)
{
    while (s.substr(0, 1) == '0' && s.length > 1)
    {
        s = s.substr(1, 9999);
    }
    return s;
}

/*
 Change the status of the ticket from here by ajax
 */
function SetTicketStatus(TICKET, STATUS, WORKGROUP) {
    $jMaQma.ajax({
        url:SITEURL + 'index.php?option=com_maqmahelpdesk&task=ticket_setstatus&id_workgroup=' + WORKGROUP + '&ticket=' + TICKET + '&status=' + STATUS + '&format=raw&tmpl=component',
        dataType:"html",
        error:function (data) {
        },
        success:function (data) {
            var msg = data.split("|");
            $jMaQma("#statuschange" + TICKET).html(msg[0]+'<span class="caret"></span>');
            $jMaQma("#adminForm").parent().prepend('<div class="alert alert-success">' + msg[1] + '</div>');
        }
    });
}

function SetTicketAssignment(TICKET, USER) {
    alert('change ticket ' + TICKET + ' support user to ' + USER);
}

function TicketSticky(OBJ, TICKET, WORKGROUP, ITEMID, ACTION, TABLE) {
    $jMaQma(OBJ).find("img").attr("src", SITEURL + "components/com_maqmahelpdesk/images/loading.gif");
    $jMaQma.ajax({
        url:SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + ITEMID + '&task=ticket_sticky&id_workgroup=' + WORKGROUP + '&ticket=' + TICKET + '&action=' + ACTION + '&format=raw&tmpl=component',
        dataType:"html",
        error:function (data) {
        },
        success:function (data) {
            var msg = data.split("|");
            $jMaQma("#adminForm").parent().prepend('<div class="alert alert-success">' + msg[1] + '</div>');
            if (ACTION) {
                $jMaQma(OBJ).find("img").attr("src", SITEURL + "/media/com_maqmahelpdesk/images/themes/" + IMQM_ICON_THEME + "/16px/lock.png");
            } else {
                $jMaQma(OBJ).find("img").attr("src", SITEURL + "/media/com_maqmahelpdesk/images/themes/" + IMQM_ICON_THEME + "/16px/unlock.png");
            }
        }
    });
}

function BugtrackerReply(WORKGROUP, CATEGORY, ITEMID, ID_BUGTRACKER) {
    if ($jMaQma("#reply").val() != '') {
        $jMaQma.ajax({
            url:SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + ITEMID + '&task=bugtracker_reply&id_workgroup=' + WORKGROUP + '&id_category=' + CATEGORY + '&id=' + ID_BUGTRACKER + '&format=raw&tmpl=component',
            dataType:"html",
            type:"post",
            data:"answer=" + $jMaQma("#reply").val(),
            error:function (data) {
            },
            success:function (data) {
                var msg = data.split("|");
                if (msg[1] != '') {
                    $jMaQma("#bugtracker").prepend('<div class="alert alert-success">' + msg[1] + '</div>');
                }
            }
        });
    }
}

function DiscussionReply(WORKGROUP, CATEGORY, ITEMID, ID_DISCUSSION)
{
    if ($jMaQma("#answer_text").val() != '') {
        $jMaQma.ajax({
            url:SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + ITEMID + '&task=discussions_answer&id_workgroup=' + WORKGROUP + '&id_category=' + CATEGORY + '&id_discussion=' + ID_DISCUSSION + '&format=raw&tmpl=component',
            dataType:"html",
            type:"post",
            data:"answer=" + $jMaQma("#answer_text").val() + "&valcalc=" + $jMaQma("#valcalc").val(),
            error:function (data) {
            },
            success:function (data) {
                var msg = data.split("|");
                if (msg[1] != '')
                {
                    $jMaQma("#discussions div.alert").remove();
                    $jMaQma("#discussions").prepend('<div class="alert alert-success">' + msg[1] + '</div>');
                    $jMaQma("#answer_text").val('').setCode('');
                    $jMaQma("#valcalc").val('');
                }
            }
        });
    }
}

function DiscussionVote(WORKGROUP, CATEGORY, ITEMID, ID_DISCUSSION, ID_MESSAGE, UPDOWN) {
    $jMaQma.ajax({
        url:SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + ITEMID + '&task=discussions_vote&id_workgroup=' + WORKGROUP + '&id_category=' + CATEGORY + '&id_discussion=' + ID_DISCUSSION + '&id_message=' + ID_MESSAGE + '&updown=' + UPDOWN + '&format=raw&tmpl=component',
        dataType:"html",
        error:function (data) {
        },
        success:function (data) {
            var msg = data.split("|");
            if (msg[1] != '') {
                msgTitle = ( msg[0] == 0 ? 'error' : 'success' );
                msgImage = ( msg[0] == 0 ? 'error.png' : 'success.png' );
                $jMaQma("#discussions").prepend('<div class="alert alert-' + msg[0] + '">' + msg[1] + '</div>');
            }
        }
    });
}

function DiscussionPublish(WORKGROUP, CATEGORY, ITEMID, ID_DISCUSSION, ID_MESSAGE) {
    $jMaQma.ajax({
        url:SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + ITEMID + '&task=discussions_publish&id_workgroup=' + WORKGROUP + '&id_category=' + CATEGORY + '&id_discussion=' + ID_DISCUSSION + '&id_message=' + ID_MESSAGE + '&format=raw&tmpl=component',
        dataType:"html",
        error:function (data) {
        },
        success:function (data) {
            var msg = data.split("|");
            if (msg[1] != '') {
                $jMaQma("#discussions").prepend('<div class="alert alert-success">' + msg[1] + '</div>');
                $jMaQma(".unpublished").hide();
            }
        }
    });
}

function DiscussionAccept(WORKGROUP, CATEGORY, ITEMID, ID_DISCUSSION, ID_MESSAGE) {
    $jMaQma.ajax({
        url:SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + ITEMID + '&task=discussions_accept&id_workgroup=' + WORKGROUP + '&id_category=' + CATEGORY + '&id_discussion=' + ID_DISCUSSION + '&id_message=' + ID_MESSAGE + '&format=raw&tmpl=component',
        dataType:"html",
        error:function (data) {
        },
        success:function (data) {
            var msg = data.split("|");
            if (msg[1] != '') {
                $jMaQma("#select_button").hide();
                $jMaQma("#discussions").prepend('<div class="alert alert-success">' + msg[1] + '</div>');
            }
        }
    });
}

function TicketEmailRemove(OBJ, FIELD) {
    $jMaQma(OBJ).parent().remove();
    $jMaQma("#" + FIELD).parent().css("height", parseInt($jMaQma("#" + FIELD).css("height")) + 60);
}

function SearchView(TYPE) {
    if (TYPE == 'spl') {
        $jMaQma("#filters-simple").show();
        $jMaQma("#filters").hide();
        $jMaQma("#filters2").html('');
        $jMaQma("#tview").appendTo("#filters2");
        $jMaQma(".customStyleSelectBox").appendTo("#filters2");
        $jMaQma("<span>&nbsp;</span>").appendTo("#filters2");
        $jMaQma("#searchtickets").appendTo("#filters2");
        $jMaQma("#adv-options div").appendTo("#filters1");
    } else {
        $jMaQma("#filters-simple").hide();
        $jMaQma("#filters").show();
        $jMaQma("#tview").appendTo("#adv-tview");
        $jMaQma(".customStyleSelectBox").appendTo("#adv-tview");
        $jMaQma("#searchtickets").appendTo("#adv-buttons");
        $jMaQma("#filters1 div").appendTo("#adv-options");
    }
}

function DueDatePonderado() {
    $jMaQma.ajax({
        type:"POST",
        url:SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + $jMaQma("#Itemid").val() + "&task=ajax_duedate&format=raw&p=" + $jMaQma("#id_priority").val() + "&s=" + $jMaQma("#assign_to").val() + "&w=" + $jMaQma("#id_workgroup").val(),
        success:function (msg) {
            DueDatePonderadoHandleResponse(msg);
        }
    });
}

function DueDatePonderadoHandleResponse(response) {
    var due = response.split(" ");
    $jMaQma("#duedate_date").val(due[0]);
    $jMaQma("#duedate_hours").val(due[1]);
}

function BulkPrint()
{
    if ($jMaQma('input.ticketchk:checked').length > 0)
    {
        IDS = '';
        $jMaQma.each($jMaQma("input.ticketchk:checked"), function (index, value) {
            IDS += $jMaQma(this).val() + ',';
        });
        $jMaQma("#bulkactions #ids").val(IDS);
        $jMaQma("#bulkactions #task").val("pdf_ticketbulk");
        $jMaQma("#bulkactions").attr("target", "_blank").submit();
    }
}

function CreateSlug(FIELD) {
    $jMaQma.ajax({
        url:"index.php?option=com_maqmahelpdesk&task=ajax_slug&title=" + $jMaQma("#" + FIELD).val() + "&format=raw",
        success:function (data) {
            $jMaQma("#slug").val(data);
        }
    });
}

function IsLeap(ano, mes) {
    var IsLeap = parseInt(ano);
    IsLeap = !( IsLeap % 4 ) && ( ( IsLeap % 100 ) || !( IsLeap % 400 ) );
    IsLeap = [31, (IsLeap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][mes - 1];
    return IsLeap;
}

$jMaQma(document).ready(
    function()
    {
        $jMaQma('.showPopover').popover({'html':true, 'placement':'bottom', 'trigger':'hover'});
        $jMaQma('.showPopoverRight').popover({'html':true, 'placement':'right', 'trigger':'hover'});
        $jMaQma('.showPopoverLeft').popover({'html':true, 'placement':'left', 'trigger':'hover'});
        $jMaQma('.showPopoverLarger').popover({'template':'<div class="popover larger"><div class="arrow"></div><div class="popover-inner larger"><h3 class="popover-title"></h3><div class="popover-content"><p></p></div></div></div>', 'html':true, 'placement':'bottom', 'trigger':'hover'});

        $jMaQma(".alertclose").click(function(){
            $jMaQma('#alertMessage').modal('hide');
        });
        if ($jMaQma(".artbycat").length > 0 )
        {
            $jMaQma(".artbycat").equalHeights();
        }
        if ($jMaQma(".discussion-category").length > 0 )
        {
            $jMaQma(".discussion-category").equalHeights();
        }
        if ($jMaQma(".equalheight").length > 0 )
        {
            $jMaQma(".equalheight").equalHeights();
            $jMaQma(".equalheight").css("height",($jMaQma(".equalheight").css("height")+10));
        }
        $jMaQma('.tooltip').tooltip();
        $jMaQma('.tooltip-left').tooltip({placement:'left'});
        $jMaQma('.tooltip-right').tooltip({placement:'right'});
        $jMaQma('.tooltip-bottom').tooltip({placement:'bottom'});
        $jMaQma('.item_preview pre').addClass('prettyprint').addClass('linenums');

        // Administration UI
        if( parseInt($jMaQma("#infobox").css("height")) < parseInt($jMaQma("#contentbox").css("height")) ) {
            $jMaQma("#infobox").css("height", $jMaQma("#contentbox").css("height"));
        }
        $jMaQma('#infobox').css("width",(parseInt($jMaQma('#infobox').css("width"))-2)+"px");

        // Search tweek for public discussions and kb
        if($jMaQma("#maqmaSearchForm").length > 0 || $jMaQma("#maqmaSearchBugs").length > 0) {
            if($jMaQma("#maqmaSearchForm").length > 0){
                width1 = $jMaQma("#maqmaSearchForm").width();
            }else{
                width1 = $jMaQma("#maqmaSearchBugs").width();
            }
            width2 = $jMaQma("#postQuestion").width();
            width3 = $jMaQma("#searchDiscussions").width();
            width4 = (width1 - width2 - width3 - 75);
            $jMaQma("#searchinput").width(width4);
        }

        // Close help box in administration
        $jMaQma("#mqmCloseHelp").click(function(){
            $jMaQma("#infobox").hide();
        });

        prettyPrint();

        //var buttons_editor = ['html', '|', 'formatting', '|', 'bold', 'italic', 'underline', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'image', 'video', 'file', 'table', 'link', '|', 'fontcolor', 'backcolor', '|', 'alignment', '|', 'horizontalrule'];
        var buttons_agent = ['html', '|', 'formatting', '|', 'bold', 'italic', 'underline', 'deleted', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'link', '|', 'fontcolor', 'backcolor'];
        var buttons_user = ['bold', 'italic', 'underline', '|', 'unorderedlist', 'orderedlist', 'outdent', 'indent', '|', 'fontcolor', 'backcolor'];
        $jMaQma('.redactor_agent').redactor({
            focus: false,
            buttons: buttons_agent,
            plugins: ['replies']
        });
        $jMaQma('.redactor_user').redactor({
            focus: false,
            buttons: buttons_user
        });
        if ($jMaQma.browser.msie == true)
        {
            $jMaQma(".maqmahelpdesk .nav-collapse.collapse").css("height", "auto")
                                                            .css("overflow", "visible");
        }
    }
);

function FileNotify( ITEMID )
{
	$jMaQma.ajax({
		type: "POST",
		url: SITEURL+"index.php?option=com_maqmahelpdesk&Itemid="+ITEMID+"&task=ticket_notify&format=raw",
		data: "id="+$jMaQma("#id").val()
	});
}

function BulkDelete()
{
    if ($jMaQma('input.ticketchk:checked').length > 0)
    {
        IDS = '';
        $jMaQma.each($jMaQma("input.ticketchk:checked"), function (index, value) {
            IDS += $jMaQma(this).val() + ',';
        });
        $jMaQma("#bulkactions #ids").val(IDS);
        $jMaQma("#bulkactions #task").val("ticket_delete");
        $jMaQma("#bulkactions").attr("target", "_self").submit();
    }
}

function SetTicketStatusBulk(STATUS)
{
    if ($jMaQma('input.ticketchk:checked').length > 0)
    {
        IDS = '';
        $jMaQma.each($jMaQma("input.ticketchk:checked"), function (index, value) {
            IDS += $jMaQma(this).val() + ',';
        });
        $jMaQma("#bulkactions #ids").val(IDS);
        $jMaQma("#bulkactions").append('<input type="hidden" name="status" value="' + STATUS + '" />');
        $jMaQma("#bulkactions #task").val("ticket_setstatus");
        $jMaQma("#bulkactions").attr("target", "_self").submit();
    }
}