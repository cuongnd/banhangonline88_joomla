Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'calendar') {
        Joomla.submitform(pressbutton);
        return;
    }

    if (form.id_user.value == "0") {
        alert(IMQM_USER_REQUIRED);
    } else if (form.date_time.value == "") {
        alert(IMQM_DATE_REQUIRED);
    } else if (form.taskmin.value == "") {
        alert(IMQM_HOURS_REQUIRED);
    } else {
        Joomla.submitform(pressbutton, document.getElementById('adminForm'));
    }
}

$jMaQma(document).ready(function () {
    $jMaQma(".timepicker").timepicker();
});