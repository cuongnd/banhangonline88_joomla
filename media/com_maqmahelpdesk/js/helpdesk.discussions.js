var MaQmaDiscussions = {};

$jMaQma(document).ready(function () {
	$jMaQma("#title").focus();
	$jMaQma(".inner-border").mouseover(
		function () {
			$jMaQma(this).find(".details").show();
		}).mouseout(function () {
			$jMaQma(this).find(".details").hide();
		});
});

function ValidateForm() {
	var form = document.adminForm;

	if (form.title.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_QA_TITLE);
		$jMaQma("#alertMessage").modal('show');
		form.title.focus();
		return false;
	} else if (form.question_content.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_QA_QUESTION);
		$jMaQma("#alertMessage").modal('show');
		form.question_content.focus();
		return false;
	} else if (form.tags.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_QA_TAGS);
		$jMaQma("#alertMessage").modal('show');
		form.tags.focus();
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
	if (confirm(MQM_QA_CANCEL)) {
		javascript:history.go(-1);
	}
}