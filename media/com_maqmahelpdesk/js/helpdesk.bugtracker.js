var MaQmaBugTracker = {};

$jMaQma(document).ready(function () {
	$jMaQma("#title").focus();
});
	
function ValidateForm() {
	var form = document.adminForm;

	if (form.title.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_BUG_TITLE);
		$jMaQma("#alertMessage").modal('show');
		form.title.focus();
		return false;
	} else if (form.priority.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_BUG_PRIORITY);
		$jMaQma("#alertMessage").modal('show');
		form.priority.focus();
		return false;
	} else if (form.type.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_BUG_TYPE);
		$jMaQma("#alertMessage").modal('show');
		form.type.focus();
		return false;
	} else if (form.description.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_BUG_DESC);
		$jMaQma("#alertMessage").modal('show');
		form.content.focus();
		return false;
	} else if (form.id_category.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_BUG_CATEGORY);
		$jMaQma("#alertMessage").modal('show');
		form.content.focus();
		return false;
	}
	return true;
}

function submitbutton() {
	var form = document.adminForm;
	if (ValidateForm() == true) {
		document.adminForm.submit();
	}
}

function Cancel() {
	if (confirm(MQM_BUG_CANCEL)) {
		javascript:history.go(-1);
	}
}