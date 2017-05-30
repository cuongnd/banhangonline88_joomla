var statusFly = new Array();

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

    GetLabourTime();
    GetStatusFlyOriginal();

    // Set scroll for status floating
    $jMaQma(window).scroll(function(){
        SetFlyingStatus()
    });

    if (MQM_STATUS_GROUP == 'C')
    {
        $jMaQma("#rating").rater(SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_rating&id=" + $jMaQma("#id").val() + "&format=raw&task2=ticket_rate", {style:"basic", maxvalue:5, curvalue:MQM_TICKET_RATE});
    }

    // Google Analytics Tracking
    if (IMQM_ANALYTICS)
    {
        $jMaQma("#ticket_reply").click(function () {
            _gaq.push(['_trackEvent', 'New ticket reply', 'Customer']);
        });
    }

    $jMaQma("#id_status").change(function(){
        $jMaQma("#statuschange").remove();
        $jMaQma(this).parent().append('<button id="statuschange" type="button" onclick="submitbutton(\'ticket_reply\');" class="btn btn-success">'+MQM_SAVE+'</button>');
    });
});

function AddAttachment() {
    $jMaQma("#AddAttachment").slideToggle();
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

    if (JSValidDueDate() == true) {
        $jMaQma("#submitButtons button").hide();
        $jMaQma("#submitButtons small").hide();
        $jMaQma("#submitButtons").append('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> ' + MQM_LOADING);
        form.submit();
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
    if (document.adminForm.travel_time[1].checked) {
        document.adminForm.tickettravel.value = MQM_CLIENT_TRAVEL;
    } else {
        document.adminForm.tickettravel.value = '0.00';
    }

    if (document.addTask.tasktravel[1].checked) {
        document.addTask.traveltime.value = MQM_CLIENT_TRAVEL;
    } else {
        document.addTask.traveltime.value = '0.00';
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

function GetStatusFlyOriginal()
{
    statusFly['position']	 	  = $jMaQma("#id_status").parent().css("position");
    statusFly['top']			  = $jMaQma("#id_status").parent().css("top");
    statusFly['background-color'] = $jMaQma("#id_status").parent().css("background-color");
    statusFly['z-index']	 	  = $jMaQma("#id_status").parent().css("z-index");
    statusFly['padding-top']   	  = $jMaQma("#id_status").parent().css("padding-top");
    statusFly['border-bottom']	  = $jMaQma("#id_status").parent().css("border-bottom");
    statusFly['width']		  	  = $jMaQma("#id_status").parent().css("width");
}

function SetFlyingStatus()
{
    /*if ($jMaQma("#id_status").length > 0) {
        if ($jMaQma("#id_status").parent().offset().top < window.pageYOffset) {
            $jMaQma("#id_status").parent()
                .css("position",		 "fixed")
                .css("bottom",			 "0")
                .css("background-color", "#fff")
                .css("z-index", 		 "1000")
                .css("padding-top",	     "10px")
                .css("border-top",	     "2px solid #ccc")
                .css("width",		     $jMaQma("#id_status").parent().parent().width());
        }else{
            $jMaQma("#id_status").parent()
                .css("position",		 statusFly['position'])
                .css("bottom",			 statusFly['top'])
                .css("background-color", statusFly['background-color'])
                .css("z-index",		     statusFly['z-index'])
                .css("padding-top",	     statusFly['padding-top'])
                .css("border-bottom",	 statusFly['border-bottom'])
                .css("width",			 statusFly['width']);
        }
    }*/
}