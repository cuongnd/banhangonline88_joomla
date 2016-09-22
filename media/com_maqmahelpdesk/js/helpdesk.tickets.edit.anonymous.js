var statusFly = new Array();

function AddAttachment()
{
    $jMaQma("#AddAttachment").slideToggle();
}

function Cancel()
{
    if (confirm(MQM_CANCEL)) {
        window.location = MQM_CANCEL_LINK;
    }
}

function submitbutton(pressbutton)
{
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
    document.adminForm.tickettravel.value = '0.00';
    document.addTask.traveltime.value = '0.00';
}

$jMaQma(document).ready(function ($) {
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

    // Google Analytics Tracking
    if (IMQM_ANALYTICS)
    {
        $jMaQma("#ticket_reply").click(function () {
            _gaq.push(['_trackEvent', 'New ticket reply', 'Anonymous']);
        });
    }

    GetStatusFlyOriginal();

    // Set scroll for status floating
    $jMaQma(window).scroll(function(){
        SetFlyingStatus()
    });
});

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
    /*if ($jMaQma("#id_status").parent().offset().top < window.pageYOffset) {
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
    }*/
}