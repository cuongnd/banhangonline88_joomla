function AddAttachment()
{
    $jMaQma("#AddAttachment").slideToggle();
}

function submitbutton(pressbutton)
{
    var form = document.adminForm;
    if (pressbutton == 'ticket_save') {
        form.task.value = 'ticket_save';
        if (ValidateForm() == true && JSValidDueDate() == true) {
            $jMaQma("#submitButtons button").hide();
            $jMaQma("#submitButtons small").hide();
            $jMaQma("#submitButtons").append('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> ' + MQM_LOADING);
            document.adminForm.submit();
        }
    }
}

function ValidateForm()
{
    var form = document.adminForm;
    if (form.subject.value == "") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_MSG01);
        $jMaQma("#alertMessage").modal('show');
        form.subject.focus();
        return false;
    } else if (MQM_IS_ANONYMOUS && form.an_name.value == "") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_NAME);
        $jMaQma("#alertMessage").modal('show');
        return false;
    } else if (MQM_IS_ANONYMOUS && form.an_mail.value == "") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_EMAIL);
        $jMaQma("#alertMessage").modal('show');
        return false;
    } else if (form.problem.value == "") {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_QUESTION);
        $jMaQma("#alertMessage").modal('show');
        form.problem.focus();
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

    // Anonymous calculation check
    if($jMaQma("#an_mail").length > 0)
    {
        if($jMaQma("#valcalc").val() != MQM_CALC_VAL)
        {
            $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_VALCALC);
            $jMaQma("#alertMessage").modal('show');
            return false;
        }
    }

    return true;
}

function Cancel()
{
    if (confirm(MQM_CANCEL)) {
        window.location = MQM_CANCEL_LINK;
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

function SetTravelTime()
{
    if (document.adminForm.travel_time[1].checked) {
        document.adminForm.tickettravel.value = MQM_CLIENT_TRAVEL;
    } else {
        document.adminForm.tickettravel.value = '0';
    }

    if (document.addTask.tasktravel[1].checked) {
        document.addTask.traveltime.value = MQM_CLIENT_TRAVEL;
    } else {
        document.addTask.traveltime.value = '0';
    }
}

$jMaQma().ready(function () {
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
    DueDatePonderado();
    document.adminForm.subject.focus();

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

    // Google Analytics Tracking
    if (IMQM_ANALYTICS)
    {
        $jMaQma("#ticket_save").click(function () {
            _gaq.push(['_trackEvent', 'New ticket', 'Anonymous']);
        });
    }

    // Show extra notification options CC and BCC
    if (!MQM_IS_ANONYMOUS)
    {
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
    }
});

var CreateScreenr = function()
{
    $jMaQma("#ticket_save").attr("disabled",true);
    $jMaQma("#button_to_record").hide();
    $jMaQma("#is_recording").show();

    // Initialize the recorder
    var recorder = Screenr.Recorder({
        id:MQM_SCREENR_API,
        userName:MQM_USER_NAME,
        userEmail:MQM_USER_EMAIL,
        subject:MQM_SCREENR_RECORDER_TITLE,
        showNameField:false,
        showEmailField:false,
        showDescriptionField:false
    });
    // recorder.addCustomData("ticketId", 1);
    recorder.setOnComplete(function(screencast) {
        $jMaQma("#ticket_save").attr("disabled",false);
        $jMaQma("#is_recording").html(MQM_SCREENR_MSG_COMPLETE + " - " + screencast.url);
        $jMaQma('#adminForm').append($jMaQma('<input/>', {
            type: 'hidden',
            name: 'screenr_url',
            value: screencast.url
        }));
        $jMaQma('#adminForm').append($jMaQma('<input/>', {
            type: 'hidden',
            name: 'screenr_embed',
            value: screencast.embed
        }));
        $jMaQma('#adminForm').append($jMaQma('<input/>', {
            type: 'hidden',
            name: 'screenr_embedurl',
            value: screencast.embedUrl
        }));
        $jMaQma('#adminForm').append($jMaQma('<input/>', {
            type: 'hidden',
            name: 'screenr_thumbnailurl',
            value: screencast.thumbnailUrl
        }));
        $jMaQma('#adminForm').append($jMaQma('<input/>', {
            type: 'hidden',
            name: 'screenr_id',
            value: screencast.id
        }));
    });
    /*
     embed - its an iframe
     embedUrl - its the url for the screencast
     id - id of the screencast
     thumbnailUrl -
     url -
     */
    recorder.setOnCancel(function(screencast) {
        $jMaQma("#ticket_save").attr("disabled",false);
        $jMaQma("#button_to_record").hide();
        $jMaQma("#is_recording").show();
    });
    recorder.record();
}
