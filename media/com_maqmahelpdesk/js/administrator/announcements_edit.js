Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'announce') {
        Joomla.submitform(pressbutton);
        return;
    }

    if (form.introtext.value == "") {
        alert(IMQM_TITLE_REQUIRED);
        return false;
    } else {
        if (!CheckHTMLEditor())
        {
            return false;
        }
        Joomla.submitform(pressbutton, document.getElementById('adminForm'));
    }
}