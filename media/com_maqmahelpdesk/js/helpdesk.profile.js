var MaQmaUserProfile = {};

function SelectAvatar(AVATAR) {
	$jMaQma("#avatar").val(SITEURL+'media/com_maqmahelpdesk/images/avatars/' + AVATAR + '.png');
	$jMaQma(".alert.alert-block").hide();
	$jMaQma("#profileForm").prepend('<div class="alert alert-block"><h4 class="alert-heading">'+MQM_USER_WARNING+'!</h4>'+MQM_USER_SAVE+'</div>');
	$jMaQma("#selectedavatar").attr('src', SITEURL+'media/com_maqmahelpdesk/images/avatars/' + AVATAR + '.png');
	$jMaQma('#avatars_list').modal('hide');
}

function submitbutton_reg() {
	var form = document.profileForm;

	// do field validation
	if (form.name.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_NAME);
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.email.value == "") {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_EMAIL);
		$jMaQma("#alertMessage").modal('show');
		return false;
		return false;
	} else if (form.password.value != "" && form.password2.value == '' && MQM_USER_SHOW_LOGIN) {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_PASS);
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.password.value != "" && form.password2.value != '' && form.password.value != form.password2.value && MQM_USER_SHOW_LOGIN) {
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_PASS);
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.phone.value == "" && MQM_USER_RF_PHONE) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_PHONE);
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.fax.value == "" && MQM_USER_RF_FAX) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_FAX);
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.mobile.value == "" && MQM_USER_RF_MOBILE) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_MOBILE);
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.address1.value == "" && MQM_USER_RF_ADDRESS1) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_ADDRESS1); 
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.address2.value == "" && MQM_USER_RF_ADDRESS2) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_ADDRESS2); 
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.zipcode.value == "" && MQM_USER_RF_ZIP) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_ZIP);
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.location.value == "" && MQM_USER_RF_LOCATION) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_LOCATION); 
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.city.value == "" && MQM_USER_RF_CITY) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_CITY);
		$jMaQma("#alertMessage").modal('show');
		return false;
	} else if (form.country.value == "" && MQM_USER_RF_COUNTRY) { 
		$jMaQma("#alertMessage .modal-body p").html('<img src="'+SITEURL+'components/com_maqmahelpdesk/images/alert.png" align="absmiddle" /> '+MQM_USER_COUNTRY);
		$jMaQma("#alertMessage").modal('show');
		return false;
	}else if(CheckCustomFields() == false) {
		return false;
	} else {
		return true;
	}
}

$jMaQma(document).ready(function () {
    $jMaQma("#country").val(MQM_USER_IS_COUNTRY);
});
