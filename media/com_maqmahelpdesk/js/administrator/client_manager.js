Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'show_help') {
        $jMaQma("#infobox").show();
        return;
    }

    Joomla.submitform(pressbutton, document.getElementById('adminForm'));
}

$jMaQma(document).ready(function () {
    $jMaQma("#filter").css("width", $jMaQma("#filtersarea").width() - $jMaQma("#filter").offset().left - $jMaQma(".btn-group").width());
    $jMaQma(window).resize(function () {
        $jMaQma("#filter").css("width", $jMaQma("#filtersarea").width() - $jMaQma("#filter").offset().left - $jMaQma(".btn-group").width());
    });
});