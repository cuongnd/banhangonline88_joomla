Joomla.submitbutton = function (pressbutton) {
    var form = document.adminForm;
    if (pressbutton == 'category') {
        Joomla.submitform(pressbutton);
        return;
    }

    if (form.name.value == "")
    {
        alert(IMQM_DESCRIPTION_REQUIRED);
    }
    else if (form.id_workgroup.value == 0)
    {
        alert(IMQM_DEPARTMENT_REQUIRED);
    }
    else
    {
        Joomla.submitform(pressbutton, document.getElementById('adminForm'));
    }
}

$jMaQma(document).ready(function () {
    if($jMaQma("#id").val() > 0){
        GetCategories();
    }
    $jMaQma('.showPopover').popover({'html':true, 'trigger':'hover'});
});

function GetCategories() {
    $jMaQma.ajax({
        url:"index.php?option=com_maqmahelpdesk&task=category_categories&id_workgroup=" + $jMaQma("#id_workgroup").val() + "&format=raw",
        success:function (data) {
            $jMaQma("#categoryField").html(data);
            if($jMaQma("#id").val() > 0){
                $jMaQma("#parentField").html(data);
            }
        }
    });
}