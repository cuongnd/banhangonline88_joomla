Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'show_help') {
        $jMaQma("#infobox").show();
        return;
    }

    Joomla.submitform(pressbutton, document.getElementById('adminForm'));
}

$jMaQma(document).ready(function () {
    $jMaQma('.showPopover').popover({'html':true, 'placement':'right', 'trigger':'hover'});
});