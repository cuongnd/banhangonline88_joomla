var MaQmaKB = {};

$jMaQma(document).ready(function () {
	document.adminForm.code.focus();
	FillCategories();
});

function Cancel()
{
	if (confirm("Are you sure you want to cancel? "))
    {
		javascript:history.go(-1);
	}
}

function TreatCategories()
{
	var form = document.adminForm;

	for (i = 0; i < form.id_category.length; i++)
    {
		if (form.id_category[i].selected == true)
        {
			form.categories.value = form.categories.value + form.id_category[i].value + ",";
		}
	}
}

function FillCategories()
{
	form = document.adminForm;

	EMPS1 = MQM_KB_CAT_LIST;
	EMPS = EMPS1.split(",");

	for (z = 0; z < EMPS.length; z++)
    {
		for (i = 0; i < form.id_category.length; i++)
        {
			if (form.id_category[i].value == EMPS[z])
            {
				form.id_category[i].selected = true;
			}
		}
	}
}

function increaseNotesHeight(thisTextarea, add)
{
	if (thisTextarea)
    {
		newHeight = parseInt(thisTextarea.style.height) + add;
		thisTextarea.style.height = newHeight + "px";
	}
}

function decreaseNotesHeight(thisTextarea, subtract)
{
	if (thisTextarea)
    {
		if ((parseInt(thisTextarea.style.height) - subtract) > 150)
        {
			newHeight = parseInt(thisTextarea.style.height) - subtract;
			thisTextarea.style.height = newHeight + "px";
		}
        else
        {
			newHeight = 150;
			thisTextarea.style.height = "150px";
		}
	}
}
function formvalidate()
{
	var form = document.adminForm;

	if (form.title.value == "")
    {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_KB_TITLE);
		$jMaQma("#alertMessage").modal('show');
	}
    else if (!getSelectedValue('adminForm', 'id_category'))
    {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_KB_CATEGORY);
		$jMaQma("#alertMessage").modal('show');
	}
    else
    {
		CheckHTMLEditor();
		TreatCategories();
		form.submit();
	}
}