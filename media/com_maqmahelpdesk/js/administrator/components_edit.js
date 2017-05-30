Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'components') {
        Joomla.submitform(pressbutton);
        return;
    }

    if (form.name.value == "") {
        alert(IMQM_NAME_REQUIRED);
    } else {
        Joomla.submitform(pressbutton, document.getElementById('adminForm'));
    }
}