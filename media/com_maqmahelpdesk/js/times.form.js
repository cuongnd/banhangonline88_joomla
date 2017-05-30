$jMaQma(document).ready(function () {
    $jMaQma(".timepicker").timepicker();

    $jMaQma("#getclient").autocompletemqm(SITEURL + "index.php?option=com_maqmahelpdesk&Itemid=" + MQM_ITEMID + "&task=ajax_getclient&format=raw", {
        selectFirst:false,
        scroll:true,
        scrollHeight:300,
        formatItem:function (data, i, n, value) {
            return '<img src="' + data[2] + '" width="32" height="32" align="left">' + data[1];
        },
        selectExecute:function (data) {
            $jMaQma("#id_client").val(data[0]);
            $jMaQma("#getclient").val(data[1]);
        }
    });
});

function submitbutton(pressbutton)
{
    if($jMaQma("#id_client").val() == "" || $jMaQma("#id_client").val() == "0")
    {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_CLIENT);
        $jMaQma("#alertMessage").modal('show');
        $jMaQma("#getclient").focus();
        return false;
    }
    if($jMaQma("#year").val() == "")
    {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_YEAR);
        $jMaQma("#alertMessage").modal('show');
        $jMaQma("#year").focus();
        return false;
    }
    if($jMaQma("#month").val() == "")
    {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_MONTH);
        $jMaQma("#alertMessage").modal('show');
        $jMaQma("#month").focus();
        return false;
    }
    if($jMaQma("#day").val() == "")
    {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_DAY);
        $jMaQma("#alertMessage").modal('show');
        $jMaQma("#day").focus();
        return false;
    }
    if($jMaQma("#time").val() == "")
    {
        $jMaQma("#alertMessage .modal-body p").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> ' + MQM_TIME);
        $jMaQma("#alertMessage").modal('show');
        $jMaQma("#time").focus();
        return false;
    }
    $jMaQma(".form-actions").html('<img src="' + SITEURL + 'components/com_maqmahelpdesk/images/loading.gif" alt="" align="absmiddle" /> ' + MQM_LOADING);
    document.adminForm.submit();
}