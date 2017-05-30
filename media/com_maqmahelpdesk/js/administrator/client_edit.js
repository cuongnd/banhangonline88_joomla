function ShowProgress() {
    middle_width = screen.width / 2;
    middle_height = screen.height / 2;

    $jMaQma("#Layer1").css('left', middle_width - 100);
    $jMaQma("#Layer1").css('top', middle_height - 250);
    $jMaQma("#Layer1").show('fade');
}

Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'client') {
        Joomla.submitform(pressbutton);
        return;
    }

    /*WKSObj = document.adminForm.id_workgroup;
    var j = 0;
    h = 0;
    for (j = 0; j < WKSObj.length; j++) {
        if (WKSObj[j].selected) {
            h = h + 1;
        }
    }

    PrepareWks();*/

    if (form.clientname.value == "") {
        alert(IMQM_NAME_REQUIRED);
    } else {
        Joomla.submitform(pressbutton, document.getElementById('adminForm'));
    }
}

function FillWks() {
    GRPS1 = document.adminForm.wks.value;
    GRPS = GRPS1.split(/\s*,\s*/);

    for (i = 0; i < document.adminForm.id_workgroup.length; i++) {
        for (z = 0; z < GRPS.length; z++) {
            if (document.adminForm.id_workgroup[i].value == GRPS[z]) {
                document.adminForm.id_workgroup[i].selected = true;
            }
        }
    }
}

function PrepareWks() {
    WKSObj = document.adminForm.id_workgroup;
    document.adminForm.wks.value = '';
    WKSVal = '';

    var j = 0;
    for (j = 0; j < WKSObj.length; j++) {
        if (WKSObj[j].selected) {
            WKSVal = WKSVal + WKSObj[j].value + ",";
        }
    }

    document.adminForm.wks.value = WKSVal.substring(0, WKSVal.length - 1);
}

function PrepareComponents()
{
    CompsObj = document.addContract.id_component;
    document.addContract.components.value = '';
    CompsVal = '';

    var j = 0;
    for (j = 0; j < CompsObj.length; j++) {
        if (CompsObj[j].selected) {
            CompsVal = CompsVal + CompsObj[j].value + ",";
        }
    }

    document.addContract.components.value = CompsVal.substring(0, CompsVal.length - 1);
}

function FillComponents()
{
    GRPS1 = document.addContract.contract_components.value;
    GRPS = GRPS1.split("|");

    for (i = 0; i < document.addContract.id_component.length; i++)
    {
        document.addContract.id_component[i].selected = false;

        for (z = 0; z < GRPS.length; z++)
        {
            if (document.addContract.id_component[i].value == GRPS[z])
            {
                document.addContract.id_component[i].selected = true;
                $jMaQma("#id_component").children("option[value="+GRPS[z]+"]").attr("selected","selected");
            }
        }
    }
}

function SetContractTitle(TITLE) {
    document.getElementById("contract_title").innerHTML = TITLE;
}

function ToggleDepartmentAccess(ID)
{
    $jMaQma("#depaccess"+ID+"_area").toggle();
    if(!$jMaQma("#depaccess"+ID+"_area").is(":visible"))
    {
        $jMaQma("#app_announcements_"+ID+"0").prop('checked',false);
        $jMaQma("#app_bugtracker_"+ID+"0").prop('checked',false);
        $jMaQma("#app_discussions_"+ID+"0").prop('checked',false);
        $jMaQma("#app_glossary_"+ID+"0").prop('checked',false);
        $jMaQma("#app_trouble_"+ID+"0").prop('checked',false);
        $jMaQma("#app_downloads_"+ID+"0").prop('checked',false);
        $jMaQma("#app_kb_"+ID+"0").prop('checked',false);
        $jMaQma("#app_faq_"+ID+"0").prop('checked',false);
        $jMaQma("#app_ticket_"+ID+"0").prop('checked',false);
        $jMaQma("#app_announcements_"+ID+"1").prop('checked',false);
        $jMaQma("#app_bugtracker_"+ID+"1").prop('checked',false);
        $jMaQma("#app_discussions_"+ID+"1").prop('checked',false);
        $jMaQma("#app_glossary_"+ID+"1").prop('checked',false);
        $jMaQma("#app_trouble_"+ID+"1").prop('checked',false);
        $jMaQma("#app_downloads_"+ID+"1").prop('checked',false);
        $jMaQma("#app_kb_"+ID+"1").prop('checked',false);
        $jMaQma("#app_faq_"+ID+"1").prop('checked',false);
        $jMaQma("#app_ticket_"+ID+"1").prop('checked',false);
    }
}

$jMaQma(document).ready(function () {
    $jMaQma(".equalheight").equalHeights();

    $jMaQma("#ac_me").autocompletemqm("index.php?option=com_maqmahelpdesk&task=ajax_getuser&format=raw&session=" + IMQM_SESSION_ID, {
            selectFirst:false,
            scroll:true,
            scrollHeight:300,
            formatItem:function (data, i, n, value) {
            return '<img src="' + data[5] + '" width="32" height="32" align="left">' + data[1] + (data[3] != '' ? '<br><i>' + data[3] + '</i>' : '');
        },
        selectExecute:function (data) {
            $jMaQma("#id_user").val(data[0]);
            $jMaQma("#ac_me").val(data[1]);
        }
    });

    if($jMaQma("#id_client").val() > 0){
        //FillWks();
    }
});