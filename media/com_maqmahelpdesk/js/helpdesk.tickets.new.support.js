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
    $jMaQma("#cc_report").autocompletemqm(SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_getusermails&format=print", {
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
    $jMaQma("#bcc_report").autocompletemqm(SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_getusermails&format=print", {
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

    DueDatePonderado();
    document.adminForm.ac_me.focus();
    document.adminForm.id_client.value = 0;

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

    $jMaQma(".timepicker").timepicker();

    GetLabourTime();

    $jMaQma("#ac_me").autocompletemqm(SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_getuser&format=print", {
        selectFirst:false,
        scroll:true,
        scrollHeight:300,
        formatItem:function (data, i, n, value) {
            return '<img src="' + data[5] + '" width="32" height="32" align="left">' + data[1] + '<br />' + data[4] + (data[3] != '' ? '<br><i>' + data[3] + '</i>' : '');
        },
        selectExecute:function (data) {
            $jMaQma("#id_user").val(data[0]);
            $jMaQma("#id_client").val(data[2]);
            $jMaQma("#ac_me").val(data[1]);
            getFieldByClient(data[2],data[0]);
        }
    });

    // Google Analytics Tracking
    if (IMQM_ANALYTICS)
    {
        $jMaQma("#ticket_save").click(function () {
            _gaq.push(['_trackEvent', 'New ticket', 'Support agent']);
        });
    }

    $jMaQma('#addUserBtn').click(function () {
        $jMaQma('#username').val('');
        $jMaQma('#usermail').val('');
        $jMaQma('#userpassword').val('');
        $jMaQma('#adduser').show();
        $jMaQma('#userarea').css("opacity", "0.25");
    });

    $jMaQma('#cancelAddUserBtn').click(function () {
        $jMaQma('#username').val('');
        $jMaQma('#usermail').val('');
        $jMaQma('#userpassword').val('');
        $jMaQma('#adduser').hide();
        $jMaQma('#userarea').css("opacity", "1");
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
});

function AddAttachment() {
    if ($jMaQma("#AddAttachment").is(":visible")) {
        $jMaQma("#AddAttachment").hide();
    } else {
        $jMaQma("#AddAttachment").show();
    }
}

function AddReply() {
    if ($jMaQma("#AddReply").is(":visible")) {
        $jMaQma("#AddReply").hide();
    } else {
        $jMaQma("#AddReply").show();
    }
}

function ValidateForm() {
    var form = document.adminForm;
    if (form.subject.value == "") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_MSG01);
        $jMaQma("#alertMessage").modal('show');
        form.subject.focus();
        return false;
    } else if (form.id_user.value == "0" && form.username.value == "" && form.usermail.value == "" ) {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_MSG03);
        $jMaQma("#alertMessage").modal('show');
        return false;
    } else if (form.id_category.value == "") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_CATEGORY);
        $jMaQma("#alertMessage").modal('show');
        return false;
    } else if (form.id_status.value == "0") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_MSG02);
        $jMaQma("#alertMessage").modal('show');
        return false;
    } else if (form.id_priority.value == "0") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_MSG04);
        $jMaQma("#alertMessage").modal('show');
        return false;
    } else if (form.source.value == "0") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_MSG05);
        $jMaQma("#alertMessage").modal('show');
        return false;
    } else if (form.duedate_date.value == "") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_MSG06);
        $jMaQma("#alertMessage").modal('show');
        return false;
    } else if (!CustomFieldsValidation()) {
        return false;
    }

    return true;
}

function UserView() {
    UserValue = document.adminForm.id_user.value;
    if (UserValue == 0) {
        alert(MQM_NO_USER);
    } else {
        window.open(SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + MQM_ITEMID + '&id_workgroup=' + $jMaQma("#id_workgroup").val() + '&task=users_getuserdetails&id=' + UserValue, 'Client', 'status=yes,toolbar=yes,scrollbars=yes,titlebar=yes,menubar=yes,resizable=yes,width=800,height=600,directories=no,location=no');
    }
}

function submitbutton(pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'ticket_save') {
        form.task.value = 'ticket_save';
        if (ValidateForm() && JSValidDueDate()) {
            CheckHTMLEditor();
            $jMaQma("#submitButtons button").hide();
            $jMaQma("#submitButtons small").hide();
            $jMaQma("#submitButtons").append('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> ' + MQM_LOADING);
            document.adminForm.submit();
        } else {
            return false;
        }
    }
}

function SetTravelTime() {
    if (document.adminForm.travel_time[1].checked) {
        document.adminForm.tickettravel.value = '0';
    } else {
        document.adminForm.tickettravel.value = '0';
    }
}

function Cancel() {
    if (confirm(MQM_CANCEL)) {
        javascript:history.go(-1);
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

function getFieldByClient(CLIENT,USER)
{
    $jMaQma(".relclient").each(function(){
        var FID = $jMaQma(this).attr("id");
        $jMaQma.ajax({
            url:SITEURL + 'index.php?option=com_maqmahelpdesk&Itemid=' + MQM_ITEMID + '&id_workgroup=' + $jMaQma("#id_workgroup").val() + '&task=ticket_field&id_field=' + FID + '&id_client=' + CLIENT + '&id_user=' + USER + '&format=raw&tmpl=component',
            dataType:"html",
            error:function (data) {
            },
            success:function (data) {
                $jMaQma("#"+FID).parent().html(data);
                $jMaQma("#"+FID).prepend('<option />').val('');
            }
        });
    });
}

