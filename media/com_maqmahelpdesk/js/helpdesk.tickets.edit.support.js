var repliesModal = '<div id="repliesmodal" style="display: none;"> \
    <div id="redactor_modal_content"> \
        <div class="redactor_modal_box"> \
            <ul class="redactor_replies_box"> \
            </ul> \
        </div> \
    </div> \
    <div id="redactor_modal_footer"> \
        <a href="javascript:;" class="redactor_modal_btn redactor_btn_modal_close">'+IMQM_CLOSE+'</a> \
    </div> \
</div>';

$jMaQma(document).ready(function () {
    $jMaQma("#cc_report").autocompletemqm(SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_getusermails&format=raw", {
        selectFirst:false,
        scroll:true,
        scrollHeight:300,
        formatItem:function (data, i, n, value) {
            return data[0] + ' (' + data[1] + ')';
        },
        selectExecute:function (data) {
            $jMaQma("#cc_emails").append('<div><input type="hidden" value="' + data[0] + '" name="cc_email_address[]" />' + data[0] + ' <a href="javascript:;" onclick="TicketEmailRemove(this,\'cc_emails\');"></a></div>');
            $jMaQma("#cc_report").val('');
        }
    });
    $jMaQma("#bcc_report").autocompletemqm(SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_getusermails&format=raw", {
        selectFirst:false,
        scroll:true,
        scrollHeight:300,
        formatItem:function (data, i, n, value) {
            return data[0] + ' (' + data[1] + ')';
        },
        selectExecute:function (data) {
            $jMaQma("#bcc_emails").append('<div><input type="hidden" value="' + data[0] + '" name="bcc_email_address[]" />' + data[0] + ' <a href="javascript:;" onclick="TicketEmailRemove(this,\'bcc_emails\');"></a></div>');
            $jMaQma("#bcc_report").val('');
        }
    });
    $jMaQma('#cc_report').keypress(function (event) {
        if (event.which == '13') {
            event.preventDefault();
            $jMaQma("#cc_emails").append('<div><input type="hidden" value="' + $jMaQma("#cc_report").val() + '" name="cc_email_address[]" />' + $jMaQma("#cc_report").val() + ' <a href="javascript:;" onclick="TicketEmailRemove(this,\'cc_emails\');"></a></div>');
            $jMaQma("#cc_report").val('');
        }
    });
    $jMaQma('#bcc_report').keypress(function (event) {
        if (event.which == '13') {
            event.preventDefault();
            $jMaQma("#bcc_emails").append('<div><input type="hidden" value="' + $jMaQma("#bcc_report").val() + '" name="bcc_email_address[]" />' + $jMaQma("#bcc_report").val() + ' <a href="javascript:;" onclick="TicketEmailRemove(this,\'bcc_emails\');"></a></div>');
            $jMaQma("#bcc_report").val('');
        }
    });

    if ($jMaQma.browser.msie && parseInt($jMaQma.browser.version, 10) == 7) {
        $jMaQma("div.current dd table.contenttable").css("margin-left", "-45px");
    }

    $jMaQma(".cfield").hide();
    $jMaQma(".cat" + $jMaQma("#id_category").val()).show();
    $jMaQma(".cat0").show();
    $jMaQma(".issection").hide();
    $jMaQma(".issection").each(function(){
        var CatSection = $jMaQma(this).attr('class');
        CatSection = CatSection.replace('span12', '');
        CatSection = CatSection.replace('issection', '');
        CatSection = CatSection.replace(' ', '');
        CatSection = CatSection.replace(' cfieldsection', 'cfieldsection');
        if ($jMaQma("."+ CatSection).filter(":visible").size() > 0) {
            $jMaQma(".issection."+ CatSection).show();
        }else{
            $jMaQma(".issection."+ CatSection).hide();
        }
    });

    $jMaQma("#id_category").change(function () {
        $jMaQma(".cfield").hide();
        $jMaQma(".cat" + $jMaQma("#id_category").val()).show();
        $jMaQma(".cat0").show();
        $jMaQma(".issection").hide();
        $jMaQma(".issection").each(function(){
            var CatSection = $jMaQma(this).attr('class');
            CatSection = CatSection.replace('span12', '');
            CatSection = CatSection.replace('issection', '');
            CatSection = CatSection.replace(' ', '');
            CatSection = CatSection.replace(' cfieldsection', 'cfieldsection');
            if ($jMaQma("."+ CatSection).filter(":visible").size() > 0) {
                $jMaQma(".issection."+ CatSection).show();
            }else{
                $jMaQma(".issection."+ CatSection).hide();
            }
        });
    });

    $jMaQma("#searchclient").autocompletemqm(SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_getclient&format=raw", {
        selectFirst:false,
        scroll:true,
        scrollHeight:300,
        formatItem:function (data, i, n, value) {
            return '<img src="' + data[2] + '" width="32" height="32" align="left">' + data[1];
        },
        selectExecute:function (data) {
            $jMaQma("#id_client").val(data[0]);
            $jMaQma("#searchclient").val(data[1]);
            $jMaQma("#newclientname").html(data[1]);
            $jMaQma("#searchclient").hide();
            $jMaQma("#cancelSearchClientBtn").hide();
            $jMaQma("#newclientname").show();
            $jMaQma("#openclient").show();
            $jMaQma("#addClientBtn").show();
            $jMaQma("#searchClientBtn").show();
        }
    });

    $jMaQma(".timepicker").timepicker();
    HighlightRelatedTerms();
    GetLabourTime();

    // Google Analytics Tracking
    if (IMQM_ANALYTICS)
    {
        $jMaQma("#ticket_save").click(function () {
            _gaq.push(['_trackEvent', 'New ticket', 'Support agent']);
        });
    }

    $jMaQma("#id_status").change(function(){
        $jMaQma("#statuschange").remove();
        $jMaQma(this).parent().append('<button id="statuschange" type="button" onclick="submitbutton(\'ticket_reply\');" class="btn btn-success">'+MQM_SAVE+'</button>');
    });

    $jMaQma('#searchClientBtn').click(function () {
        $jMaQma("#searchclient").show();
        $jMaQma("#cancelSearchClientBtn").show();
        $jMaQma("#newclientname").hide();
        $jMaQma("#openclient").hide();
        $jMaQma("#addClientBtn").hide();
        $jMaQma("#searchClientBtn").hide();
    });
    $jMaQma('#cancelSearchClientBtn').click(function () {
        $jMaQma("#searchclient").hide();
        $jMaQma("#cancelSearchClientBtn").hide();
        $jMaQma("#newclientname").show();
        $jMaQma("#openclient").show();
        $jMaQma("#addClientBtn").show();
        $jMaQma("#searchClientBtn").show();
    });
    $jMaQma('#addClientBtn').click(function () {
        $jMaQma('#addclient').show();
    });
    $jMaQma('#cancelAddClientBtn').click(function () {
        $jMaQma('#clientname').val('');
        $jMaQma('#clientaddress').val('');
        $jMaQma('#clientcity').val('');
        $jMaQma('#clientzip').val('');
        $jMaQma('#clientphone').val('');
        $jMaQma('#clientwebsite').val('');
        $jMaQma('#addclient').hide();
    });
    $jMaQma('#addUserBtn').click(function () {
        $jMaQma('#username').val($jMaQma("#an_name").val());
        $jMaQma('#usermail').val($jMaQma("#an_mail").val());
        $jMaQma('#userpassword').val('');
        $jMaQma('#adduser').show();
    });
    $jMaQma('#cancelAddUserBtn').click(function () {
        $jMaQma('#username').val('');
        $jMaQma('#usermail').val('');
        $jMaQma('#userpassword').val('');
        $jMaQma('#adduser').hide();
    });

    // Pre-defined replies
    $jMaQma("body").append(repliesModal);
    $jMaQma.ajax({
        type:"POST",
        url:SITEURL + "index.php?option=com_maqmahelpdesk&task=ticket_replieseditor&format=raw&tmpl=component",
        success:function (data) {
            $jMaQma("#repliesmodal ul.redactor_replies_box").html(data);
        }
    });

    // Username and email validation
    $jMaQma("#username").blur(function(){
        checkUserLogin();
    });
    $jMaQma("#email").blur(function(){
        checkUserLogin();
    });
});

function AddLink(ID)
{
    $jMaQma("#meeting_id").val(ID);
    $jMaQma("#addlink").show();
}

function SaveLink() {
    if ($jMaQma("#meeting_link").val() != '') {
        $jMaQma.ajax({
            type:"POST",
            url:SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=meetings_link&format=raw",
            data:"id_meeting=" + $jMaQma("#meeting_id").val() + "&link=" + $jMaQma("#meeting_link").val(),
            success:function () {
                $jMaQma("#meeting_id").val('');
                $jMaQma("#meeting_link").val('');
                $jMaQma("#addlink").hide();
                window.location.reload();
            }
        });
    }
}

function AddMeeting() {
    $jMaQma("#addmeeting").show();
}

function SaveMeeting() {
    if ($jMaQma("#meeting_date").val() != '' && $jMaQma("#meeting_hours").val() != '' && $jMaQma("#meeting_invites").val() != '') {
        $jMaQma.ajax({
            type:"POST",
            url:SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=meetings_new&format=raw",
            data:"id_ticket=" + $jMaQma("#id").val() + "&date=" + $jMaQma("#meeting_date").val() + "&hours=" + $jMaQma("#meeting_hours").val() + "&" + $jMaQma("#meeting_invites").serialize(),
            success:function () {
                $jMaQma("#meeting_date").val('');
                $jMaQma("#meeting_hours").val('');
                $jMaQma("#meeting_invites").val('');
                $jMaQma("#addmeeting").hide();
                window.location.reload();
            }
        });
    }
}

function StartMeeting(OBJ, ID) {
    $jMaQma.ajax({
        type:"POST",
        url:SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=meetings_start&format=raw",
        data:"id_ticket=" + $jMaQma("#ticketmask").val() + "&id_meeting=" + ID,
        dataType:"json",
        success:function (response) {
            $jMaQma("#responsemeeting").show().html(response.message);
            if (response.joinurl != '') {
                window.open(response.joinurl);
            }
        }
    });
}

function AddAttachment() {
    if ($jMaQma("#AddAttachment").is(":visible")) {
        $jMaQma("#AddAttachment").hide();
    } else {
        $jMaQma("#AddAttachment").show();
    }
}

function AddNote() {
    $jMaQma("#AddNote").modal('show');
}

function ChangeWorkgroup(ID) {
    if (confirm(MQM_WK_TO_WK)) {
        window.location = SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&id_workgroup=" + $jMaQma("#id_workgroup").val() + "&task=ticket_changewk&id=" + $jMaQma("#id").val() + "&from=" + $jMaQma("#id_workgroup").val() + "&to=" + ID;
    }
}

function ShowDetails() {
    $jMaQma('#ExtraDetails').show();
    $jMaQma('#ExtraDetailsShow').hide();
}

function HideDetails() {
    $jMaQma('#ExtraDetails').hide();
    $jMaQma('#ExtraDetailsShow').show();
}

function Cancel() {
    if (confirm(MQM_CANCEL)) {
        window.location = MQM_CANCEL_LINK;
    }
}

function submitbutton(pressbutton) {
    var form = document.adminForm;

    if (pressbutton == 'ticket_replyapply') {
        form.task.value = pressbutton;
    } else if (pressbutton == 'ticket_reply') {
        form.task.value = pressbutton;
    }

    if(!$jMaQma("#user_is_valid").val() && $jMaQma("#username").val() != '' && $jMaQma("#usermail").val() != '')
    {
        if($jMaQma("#user_is_valid").val() == 2)
        {
            alert(MQM_USERNAME_EXISTS);
            return;
        }
        else if($jMaQma("#user_is_valid").val() == 3)
        {
            alert(MQM_USER_MAIL_EXISTS);
            return;
        }
    }

    if (JSValidDueDate() == true)
    {
        CheckHTMLEditor();
        $jMaQma("#submitButtons button").hide();
        $jMaQma("#submitButtons small").hide();
        $jMaQma("#submitButtons").append('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> ' + MQM_LOADING);
        document.adminForm.submit();
    }
}

function JSValidDueDate()
{
    valor = document.adminForm.duedate_date.value;
    valor = valor.split("-");
    var DAY = valor[2]
    var MONTH = valor[1];
    var YEAR = valor[0];

    if (MONTH < 1 || MONTH > 12)
    {
        alert(MQM_INV_MONTH);
        return false;
    }

    if (YEAR < 2000 || YEAR > MQM_YEAR1 )
    {
        alert(MQM_INV_YEAR);
        return false;
    }
    month_days = IsLeap(YEAR, MONTH);
    if (DAY < 1 || DAY > month_days)
    {
        alert(MQM_INV_DAY);
        return false;
    }
    valor = document.adminForm.duedate_hours.value;
    var MINUTES = valor.substring(3, 5);
    var HOURS = valor.substring(0, 2);
    if (MINUTES < 0 || MINUTES > 59) {
        alert(MQM_INV_MINUTES);
        return false;
    }

    if (HOURS < 0 || HOURS > 23) {
        alert(MQM_INV_HOURS);
        return false;
    }
    return true;
}

function SetTravelTime() {
    if ($jMaQma("#using_activities").val() == 1) {
        if ($jMaQma("#travel1").is(":checked")) {
            $jMaQma("#tickettravel").val(MQM_CLIENT_TRAVEL);
        } else {
            $jMaQma("#tickettravel").val('0.00');
        }
    } else {
        $jMaQma("#tickettravel").val('0.00');
    }

    if ($jMaQma("#AddTask #tasktravel1").is(":checked")) {
        $jMaQma("#AddTask #traveltime").val(MQM_CLIENT_TRAVEL);
    } else {
        $jMaQma("#AddTask #traveltime").val('0.00');
    }
}

function GetLabourTimeTasks() {
    starttime = $jMaQma("#AddTask #taskstart").val();
    endtime = $jMaQma("#AddTask #taskend").val();
    breaktime = $jMaQma("#AddTask #taskbreak").val();
    if (starttime == endtime) return;

    var hours_starttime = starttime.substring(0, 2);
    var mins_starttime = starttime.substring(3, 5);
    var hours_endtime = endtime.substring(0, 2);
    var mins_endtime = endtime.substring(3, 5);
    var hours_breaktime = breaktime.substring(0, 2);
    var mins_breaktime = breaktime.substring(3, 5);

    var starttime_decim = (hours_starttime * 1) + (mins_starttime / 60);
    var endtime_decim = (hours_endtime * 1) + (mins_endtime / 60);
    var breaks_decim = (hours_breaktime * 1) + (mins_breaktime / 60);
    var replytime_decim = (endtime_decim - starttime_decim) - (breaks_decim);

    if ((replytime_decim < 0) && starttime != endtime && breaks_decim == 0) {
        $jMaQma("#AddTask #taskend").val($jMaQma("#AddTask #taskstart").val());
    }

    if (replytime_decim > 0) {
        var replytime_hours = Math.floor(replytime_decim);
        var replytime_mins = (replytime_decim - replytime_hours) * 60;
    } else {
        var replytime_hours = Math.ceil(replytime_decim);
        var replytime_mins = ( (replytime_decim * (-1)) - (replytime_hours * (-1)) ) * 60;
    }

    if (replytime_decim < 0 && replytime_hours == 0) {
        var neg_hhmm = "-";
    } else {
        var neg_hhmm = "";
    }

    replytime_mins = Math.round(replytime_mins * Math.pow(10, 0)) / Math.pow(10, 0);

    if (replytime_mins == "0") {
        replytime_mins = "00";
    } else if (replytime_mins < 10) {
        replytime_mins = "0" + replytime_mins;
    }

    var replytime_hhmm = neg_hhmm + replytime_hours + ':' + replytime_mins;

    $jMaQma("#AddTask #tasktime").val(replytime_hhmm);

    if (replytime_decim < 0) {
        alert(MQM_LABOUR_NEGATIVE);
        $jMaQma("#AddTask #taskbreak").val("00:00");
        GetLabourTime();
    }
}

function GetLabourTime() {
    starttime = document.adminForm.start_time.value;
    endtime = document.adminForm.end_time.value;
    breaktime = document.adminForm.break_time.value;
    if (starttime == endtime) return;

    var hours_starttime = starttime.substring(0, 2);
    var mins_starttime = starttime.substring(3, 5);
    var hours_endtime = endtime.substring(0, 2);
    var mins_endtime = endtime.substring(3, 5);
    var hours_breaktime = breaktime.substring(0, 2);
    var mins_breaktime = breaktime.substring(3, 5);

    var starttime_decim = (hours_starttime * 1) + (mins_starttime / 60);
    var endtime_decim = (hours_endtime * 1) + (mins_endtime / 60);
    var breaks_decim = (hours_breaktime * 1) + (mins_breaktime / 60);
    var replytime_decim = (endtime_decim - starttime_decim) - (breaks_decim);

    if ((replytime_decim < 0) && starttime != endtime && breaks_decim == 0) {
        document.adminForm.end_time.value = document.adminForm.start_time.value;
        starttime = document.adminForm.start_time.value;
        endtime = document.adminForm.end_time.value;
        breaktime = document.adminForm.break_time.value;

        var hours_starttime = starttime.substring(0, 2);
        var mins_starttime = starttime.substring(3, 5);
        var hours_endtime = endtime.substring(0, 2);
        var mins_endtime = endtime.substring(3, 5);
        var hours_breaktime = breaktime.substring(0, 2);
        var mins_breaktime = breaktime.substring(3, 5);

        var starttime_decim = (hours_starttime * 1) + (mins_starttime / 60);
        var endtime_decim = (hours_endtime * 1) + (mins_endtime / 60);
        var breaks_decim = (hours_breaktime * 1) + (mins_breaktime / 60);
        var replytime_decim = (endtime_decim - starttime_decim) - (breaks_decim);
    }

    if (replytime_decim > 0) {
        var replytime_hours = Math.floor(replytime_decim);
        var replytime_mins = (replytime_decim - replytime_hours) * 60;
    } else {
        var replytime_hours = Math.ceil(replytime_decim);
        var replytime_mins = ( (replytime_decim * (-1)) - (replytime_hours * (-1)) ) * 60;
    }

    if (replytime_decim < 0 && replytime_hours == 0) {
        var neg_hhmm = "-";
    } else {
        var neg_hhmm = "";
    }

    replytime_mins = Math.round(replytime_mins);

    if (replytime_mins < 10) {
        replytime_mins = "0" + replytime_mins;
    }

    var replytime_hhmm = neg_hhmm + replytime_hours + ':' + replytime_mins;

    document.adminForm.replytime.value = replytime_hhmm;

    if (replytime_decim < 0) {
        alert(MQM_LABOUR_NEGATIVE);
        document.adminForm.break_time.value = "00:00";
        GetLabourTime();
    }
}

function mergeTicket() {
    if ($jMaQma("#ticket_merge #merge_number").val() != '') {
        $jMaQma("#ticket_merge .modal-footer").html('&nbsp;<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/loading.gif" align="absmiddle" />');
        $jMaQma.ajax({
            type:"POST",
            url:SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_merge&format=raw",
            data:"id_to=" + $jMaQma("#id").val() + "&id_from=" + $jMaQma("#ticket_merge #merge_number").val(),
            success:function () {
                window.location.reload();
            }
        });
    }
}

function asReplyTicket() {
    if ($jMaQma("#ticket_as_reply #ticket_as_reply_number").val() != '') {
        $jMaQma("#ticket_as_reply .modal-footer").html('&nbsp;<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/loading.gif" align="absmiddle" />');
        $jMaQma.ajax({
            type:"POST",
            url:MQM_ASREPLY_URL,
            data:"id_from=" + $jMaQma("#id").val() + "&id_to=" + $jMaQma("#ticket_as_reply #ticket_as_reply_number").val(),
            success:function (data) {
                window.location = SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + MQM_ITEMID + '&id_workgroup=' + $jMaQma("#id_workgroup").val() + '&task=ticket_view&id=' + data;
            }
        });
    }
}

function setParentTicket()
{
    if ($jMaQma("#ticket_parent #ticket_parent").val() != '') {
        $jMaQma("#ticket_parent .modal-footer").html('&nbsp;<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/loading.gif" align="absmiddle" />');
        $jMaQma.ajax({
            type:"POST",
            url:MQM_PARENT_URL,
            data:"ticket=" + $jMaQma("#ticketmask").val() + "&parent=" + $jMaQma("#ticket_parent #parent_ticket").val(),
            success:function (data) {
                window.location = document.URL;
            }
        });
    }
}

function deleteTicket()
{
    var answer = confirm(MQM_DELETE)
    if (answer) {
        window.location = MQM_DELETE_URL;
    }
}

function checkUserLogin()
{
    if ($jMaQma("#username").val() != '' && $jMaQma("#usermail").val() != '')
    {
        $jMaQma.ajax({
            type: "POST",
            url: MQM_PARENT_URL,
            data: "ticket=" + $jMaQma("#ticketmask").val() + "&parent=" + $jMaQma("#ticket_parent #parent_ticket").val(),
            success:function (data) {
                $jMaQma("#user_is_valid").val(data)
            }
        });
    }
}